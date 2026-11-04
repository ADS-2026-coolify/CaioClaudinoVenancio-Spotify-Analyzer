import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { TimeRangeSelector } from '@/components/ui/TimeRangeSelector';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import type { TimeRange } from '@/types';
import { Calendar, Clock, Disc, ExternalLink, Music, Play, Sparkles } from 'lucide-react';

export const TracksPage: React.FC = () => {
  const { isDemo } = useAuth();
  const [timeRange, setTimeRange] = useState<TimeRange>('medium_term');

  const {
    data: tracks = [],
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['tracks', timeRange, isDemo],
    queryFn: () => api.getTopTracks(timeRange, isDemo),
  });

  return (
    <div className="space-y-8 pb-12">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
            <Music className="w-4 h-4" />
            Análise de Músicas
          </div>
          <h1 className="text-3xl font-extrabold text-white mt-1">Suas Músicas Mais Ouvidas</h1>
          <p className="text-sm text-spotify-muted mt-1">
            Faixas que mais marcaram suas reproduções no período selecionado.
          </p>
        </div>

        <TimeRangeSelector value={timeRange} onChange={setTimeRange} />
      </div>

      {isLoading ? (
        <CardSkeleton count={6} />
      ) : isError ? (
        <ErrorMessage onRetry={() => refetch()} />
      ) : tracks.length === 0 ? (
        <div className="p-12 text-center text-spotify-muted">
          Nenhuma música registrada para este período.
        </div>
      ) : (
        <div className="space-y-6">
          {/* Top Track Spotlight Card */}
          {tracks[0] && (
            <div className="rounded-3xl border border-spotify-green/40 bg-gradient-to-r from-spotify-green/15 via-spotify-dark to-spotify-surface/50 p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 glow-subtle">
              <div className="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-6">
                {tracks[0].image_url ? (
                  <img
                    src={tracks[0].image_url}
                    alt={tracks[0].name}
                    className="w-28 h-28 sm:w-36 sm:h-36 rounded-2xl object-cover shadow-2xl ring-2 ring-spotify-green/50"
                  />
                ) : (
                  <div className="w-28 h-28 sm:w-36 sm:h-36 rounded-2xl bg-spotify-surface flex items-center justify-center">
                    <Music className="w-12 h-12 text-spotify-muted" />
                  </div>
                )}

                <div className="space-y-2">
                  <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-spotify-green text-black">
                    <Sparkles className="w-3.5 h-3.5 fill-current" />
                    Sua Música #1 Mais Ouvida
                  </div>
                  <h2 className="text-2xl sm:text-3xl font-extrabold text-white">
                    {tracks[0].name}
                  </h2>
                  <p className="text-base text-spotify-muted font-medium">{tracks[0].artists}</p>
                  <p className="text-xs text-spotify-subtext flex items-center justify-center sm:justify-start gap-2">
                    <Disc className="w-3.5 h-3.5" />
                    <span>{tracks[0].album}</span>
                    {tracks[0].decade && (
                      <>
                        <span>•</span>
                        <span className="font-semibold text-spotify-green">
                          {tracks[0].decade}
                        </span>
                      </>
                    )}
                  </p>
                </div>
              </div>

              {tracks[0].spotify_url && (
                <a
                  href={tracks[0].spotify_url}
                  target="_blank"
                  rel="noreferrer"
                  className="w-full md:w-auto px-6 py-3 rounded-full bg-spotify-green hover:bg-spotify-green-light text-black font-extrabold text-sm flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-lg shadow-spotify-green/20"
                >
                  <Play className="w-4 h-4 fill-current" />
                  Reproduzir no Spotify
                </a>
              )}
            </div>
          )}

          {/* Full Tracks Table / List */}
          <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-4 sm:p-6 overflow-hidden">
            <h3 className="text-lg font-bold text-white mb-5 flex items-center gap-2">
              <Disc className="w-5 h-5 text-spotify-green" />
              Lista de Faixas Analisadas ({tracks.length})
            </h3>

            <div className="divide-y divide-spotify-border/40">
              {tracks.map((track) => (
                <div
                  key={track.id}
                  className="flex items-center justify-between py-3.5 px-2 rounded-xl hover:bg-spotify-surface/40 transition-colors group"
                >
                  <div className="flex items-center gap-4 min-w-0">
                    <span className="w-6 text-center text-sm font-extrabold text-spotify-subtext group-hover:text-spotify-green transition-colors">
                      {track.rank}
                    </span>

                    {track.image_url ? (
                      <img
                        src={track.image_url}
                        alt={track.name}
                        className="w-12 h-12 rounded-xl object-cover ring-1 ring-spotify-border shrink-0"
                      />
                    ) : (
                      <div className="w-12 h-12 rounded-xl bg-spotify-surface flex items-center justify-center shrink-0">
                        <Music className="w-5 h-5 text-spotify-muted" />
                      </div>
                    )}

                    <div className="min-w-0">
                      <h4 className="font-bold text-sm text-white truncate group-hover:text-spotify-green transition-colors">
                        {track.name}
                      </h4>
                      <p className="text-xs text-spotify-muted truncate mt-0.5">{track.artists}</p>
                      <p className="text-[11px] text-spotify-subtext truncate hidden sm:block">
                        {track.album}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 sm:gap-6 shrink-0 pl-3">
                    {/* Decade Badge */}
                    {track.decade && (
                      <span className="hidden md:inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-spotify-surface text-spotify-muted border border-spotify-border">
                        <Calendar className="w-3 h-3 text-spotify-green" />
                        {track.decade}
                      </span>
                    )}

                    {/* Duration */}
                    {track.duration_formatted && (
                      <span className="hidden sm:inline-flex items-center gap-1 text-xs text-spotify-subtext">
                        <Clock className="w-3.5 h-3.5" />
                        {track.duration_formatted}
                      </span>
                    )}

                    {track.spotify_url && (
                      <a
                        href={track.spotify_url}
                        target="_blank"
                        rel="noreferrer"
                        title="Abrir no Spotify"
                        className="p-2 rounded-xl bg-spotify-surface text-spotify-muted hover:text-spotify-green hover:bg-spotify-surface-hover transition-colors"
                      >
                        <ExternalLink className="w-4 h-4" />
                      </a>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

