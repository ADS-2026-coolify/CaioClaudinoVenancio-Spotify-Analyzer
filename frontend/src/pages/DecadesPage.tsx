import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { TimeRangeSelector } from '@/components/ui/TimeRangeSelector';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import type { TimeRange } from '@/types';
import { Calendar, Disc, ExternalLink, History, Music, Play, Sparkles } from 'lucide-react';
import { Bar, BarChart, Cell, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

export const DecadesPage: React.FC = () => {
  const { isDemo } = useAuth();
  const [timeRange, setTimeRange] = useState<TimeRange>('medium_term');

  const {
    data: decadeData,
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['decades', timeRange, isDemo],
    queryFn: () => api.getDecades(timeRange, isDemo),
  });

  return (
    <div className="space-y-8 pb-12">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
            <Calendar className="w-4 h-4" />
            Sua Década Musical
          </div>
          <h1 className="text-3xl font-extrabold text-white mt-1">Viagem Pelas Décadas</h1>
          <p className="text-sm text-spotify-muted mt-1">
            Descubra as eras sonoras e anos de lançamento predominantes na sua biblioteca musical.
          </p>
        </div>

        <TimeRangeSelector value={timeRange} onChange={setTimeRange} />
      </div>

      {isLoading ? (
        <CardSkeleton count={4} />
      ) : isError || !decadeData ? (
        <ErrorMessage onRetry={() => refetch()} />
      ) : decadeData.decades.length === 0 ? (
        <div className="p-12 text-center text-spotify-muted">
          Nenhuma informação de década disponível para as faixas analisadas.
        </div>
      ) : (
        <div className="space-y-8">
          {/* Predominant Decade Hero */}
          {decadeData.predominant_decade && (
            <div className="rounded-3xl border border-spotify-green/40 bg-gradient-to-r from-spotify-green/15 via-spotify-dark to-spotify-surface/50 p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 glow-subtle">
              <div className="space-y-2 text-center sm:text-left">
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold bg-spotify-green text-black">
                  <Sparkles className="w-3.5 h-3.5 fill-current" />
                  Sua Era Musical Destaque
                </div>
                <h2 className="text-3xl sm:text-5xl font-black text-white">
                  {decadeData.predominant_decade}
                </h2>
                <p className="text-sm text-spotify-muted">
                  A maioria das suas faixas favoritas foram produzidas e lançadas nesta década.
                </p>
              </div>

              <div className="p-5 rounded-2xl bg-spotify-surface/70 border border-spotify-border text-center sm:text-right shrink-0">
                <span className="text-[11px] uppercase font-bold text-spotify-subtext block">
                  Faixas Analisadas
                </span>
                <div className="text-3xl font-black text-white mt-0.5">
                  {decadeData.total_tracks_analyzed}
                </div>
                <span className="text-xs text-spotify-green font-semibold">
                  100% catalogadas com data
                </span>
              </div>
            </div>
          )}

          {/* Chronological Bar Chart */}
          <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
            <h3 className="text-lg font-bold text-white mb-2 flex items-center gap-2">
              <History className="w-5 h-5 text-spotify-green" />
              Distribuição Cronológica das Suas Músicas
            </h3>
            <p className="text-xs text-spotify-muted mb-6">
              Concentração de faixas por década de lançamento.
            </p>

            <div className="h-64 sm:h-72 w-full">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart
                  data={decadeData.decades}
                  margin={{ top: 20, right: 20, left: -10, bottom: 5 }}
                >
                  <XAxis dataKey="label" stroke="#a7a7a7" fontSize={12} />
                  <YAxis stroke="#6a6a6a" fontSize={11} />
                  <Tooltip
                    contentStyle={{
                      backgroundColor: '#181818',
                      borderColor: '#282828',
                      borderRadius: '12px',
                      color: '#fff',
                    }}
                    formatter={(value: any) => [`${value}% das faixas`, 'Percentual']}
                  />
                  <Bar dataKey="percentage" radius={[8, 8, 0, 0]}>
                    {decadeData.decades.map((entry, index) => (
                      <Cell
                        key={`decade-cell-${index}`}
                        fill={
                          entry.label === decadeData.predominant_decade ? '#1DB954' : '#282828'
                        }
                      />
                    ))}
                  </Bar>
                </BarChart>
              </ResponsiveContainer>
            </div>
          </div>

          {/* Representative Track of Each Decade */}
          <div className="space-y-4">
            <h3 className="text-xl font-bold text-white flex items-center gap-2">
              <Disc className="w-5 h-5 text-spotify-green" />
              Faixa Mais Marcante de Cada Década
            </h3>
            <p className="text-xs text-spotify-muted">
              A música com maior popularidade dentro da sua lista para cada era correspondente.
            </p>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pt-2">
              {decadeData.decades.map((item) => {
                const track = item.representative_track;
                const isPredominant = item.label === decadeData.predominant_decade;

                return (
                  <div
                    key={item.decade}
                    className={`rounded-2xl border p-5 flex flex-col justify-between transition-all ${
                      isPredominant
                        ? 'border-spotify-green/50 bg-gradient-to-br from-spotify-green/10 via-spotify-dark to-spotify-dark glow-subtle'
                        : 'border-spotify-border bg-spotify-dark/80'
                    }`}
                  >
                    <div>
                      {/* Decade Label & Count Header */}
                      <div className="flex items-center justify-between pb-3 mb-4 border-b border-spotify-border/60">
                        <span className="text-base font-extrabold text-white flex items-center gap-2">
                          {item.label}
                          {isPredominant && (
                            <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-spotify-green text-black uppercase">
                              Top Era
                            </span>
                          )}
                        </span>
                        <span className="text-xs font-semibold text-spotify-green">
                          {item.count} faixas ({item.percentage}%)
                        </span>
                      </div>

                      {/* Track Details */}
                      {track ? (
                        <div className="flex items-center gap-3.5">
                          {track.image_url ? (
                            <img
                              src={track.image_url}
                              alt={track.name}
                              className="w-14 h-14 rounded-xl object-cover ring-1 ring-spotify-border shrink-0"
                            />
                          ) : (
                            <div className="w-14 h-14 rounded-xl bg-spotify-surface flex items-center justify-center shrink-0">
                              <Music className="w-6 h-6 text-spotify-muted" />
                            </div>
                          )}

                          <div className="min-w-0">
                            <h4 className="text-sm font-bold text-white truncate">{track.name}</h4>
                            <p className="text-xs text-spotify-muted truncate mt-0.5">
                              {track.artist}
                            </p>
                            <span className="text-[11px] text-spotify-subtext block truncate mt-0.5">
                              {track.album} ({track.release_date.slice(0, 4)})
                            </span>
                          </div>
                        </div>
                      ) : (
                        <p className="text-xs text-spotify-muted italic py-3">
                          Nenhuma faixa representativa catalogada.
                        </p>
                      )}
                    </div>

                    {track?.spotify_url && (
                      <div className="mt-4 pt-3 border-t border-spotify-border/40 flex justify-end">
                        <a
                          href={track.spotify_url}
                          target="_blank"
                          rel="noreferrer"
                          className="inline-flex items-center gap-1.5 text-xs font-semibold text-spotify-muted hover:text-spotify-green transition-colors"
                        >
                          <Play className="w-3 h-3 fill-current" />
                          Ouvir no Spotify
                          <ExternalLink className="w-3 h-3 ml-0.5" />
                        </a>
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
