import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { TimeRangeSelector } from '@/components/ui/TimeRangeSelector';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import type { TimeRange } from '@/types';
import {
  Compass,
  HelpCircle,
  Info,
  Layers,
  Sparkles,
  TrendingDown,
  TrendingUp,
} from 'lucide-react';
import {
  Bar,
  BarChart,
  Cell,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts';

const COLORS = [
  '#1DB954',
  '#1ed760',
  '#34d399',
  '#10b981',
  '#059669',
  '#047857',
  '#065f46',
  '#064e3b',
  '#6ee7b7',
  '#a7f3d0',
];

export const GenresPage: React.FC = () => {
  const { isDemo } = useAuth();
  const [timeRange, setTimeRange] = useState<TimeRange>('medium_term');

  const {
    data: genreData,
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['genres', timeRange, isDemo],
    queryFn: () => api.getGenres(timeRange, isDemo),
  });

  return (
    <div className="space-y-8 pb-12">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
            <Compass className="w-4 h-4" />
            Análise de Gêneros
          </div>
          <h1 className="text-3xl font-extrabold text-white mt-1">Sua Paisagem de Gêneros</h1>
          <p className="text-sm text-spotify-muted mt-1">
            Mapeamento dos gêneros musicais associados aos artistas que você mais ouve.
          </p>
        </div>

        <TimeRangeSelector value={timeRange} onChange={setTimeRange} />
      </div>

      {isLoading ? (
        <CardSkeleton count={4} />
      ) : isError || !genreData ? (
        <ErrorMessage onRetry={() => refetch()} />
      ) : genreData.total_unique_genres === 0 ? (
        <div className="p-12 text-center text-spotify-muted">
          Nenhum dado de gênero encontrado para este período.
        </div>
      ) : (
        <div className="space-y-8">
          {/* Obscure Genre Spotlight */}
          {genreData.obscure_genre && (
            <div className="rounded-3xl border border-purple-500/40 bg-gradient-to-r from-purple-500/15 via-spotify-dark to-spotify-surface/40 p-6 sm:p-8 relative overflow-hidden">
              <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div className="space-y-2">
                  <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/40">
                    <Sparkles className="w-3.5 h-3.5 text-purple-400" />
                    Seu Gênero Mais Obscuro
                  </div>
                  <h2 className="text-2xl sm:text-4xl font-extrabold text-white capitalize">
                    {genreData.obscure_genre.name}
                  </h2>
                  <p className="text-sm text-spotify-muted">
                    Representado em seu perfil por{' '}
                    <strong className="text-white">
                      {genreData.obscure_genre.artist_sample}
                    </strong>
                    .
                  </p>
                </div>

                <div className="p-4 rounded-2xl bg-spotify-surface/60 border border-spotify-border/60 text-right">
                  <span className="text-[11px] uppercase font-bold text-spotify-subtext block">
                    Frequência Relativa
                  </span>
                  <div className="text-2xl font-black text-purple-300">
                    {genreData.obscure_genre.percentage}%
                  </div>
                  <span className="text-[11px] text-spotify-muted">
                    {genreData.obscure_genre.count} ocorrência no perfil
                  </span>
                </div>
              </div>

              {/* Explanatory Note */}
              <div className="mt-6 pt-4 border-t border-spotify-border/40 flex items-start gap-2 text-xs text-spotify-subtext">
                <Info className="w-4 h-4 shrink-0 text-purple-400 mt-0.5" />
                <span>
                  <strong>Nota sobre a métrica:</strong> Esta classificação é uma heurística
                  calculada exclusivamente a partir da raridade relativa dos gêneros encontrados
                  nos dados do seu próprio catálogo analisado, e não uma métrica oficial do
                  Spotify.
                </span>
              </div>
            </div>
          )}

          {/* Diversity Metric & Summary Stats */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div className="rounded-2xl border border-spotify-border bg-spotify-dark/80 p-5">
              <span className="text-xs font-bold uppercase tracking-wider text-spotify-muted">
                Total de Gêneros Únicos
              </span>
              <div className="text-3xl font-extrabold text-white mt-1">
                {genreData.total_unique_genres}
              </div>
              <p className="text-xs text-spotify-subtext mt-1">
                Gêneros mapeados nos seus artistas
              </p>
            </div>

            <div className="rounded-2xl border border-spotify-border bg-spotify-dark/80 p-5">
              <span className="text-xs font-bold uppercase tracking-wider text-spotify-muted">
                Gênero Predominante
              </span>
              <div className="text-3xl font-extrabold text-spotify-green mt-1 truncate">
                {genreData.top_genres[0]?.name || 'N/A'}
              </div>
              <p className="text-xs text-spotify-subtext mt-1">
                {genreData.top_genres[0]?.percentage || 0}% de concentração sonora
              </p>
            </div>

            <div className="rounded-2xl border border-spotify-border bg-spotify-dark/80 p-5">
              <span className="text-xs font-bold uppercase tracking-wider text-spotify-muted">
                Índice de Diversidade
              </span>
              <div className="text-3xl font-extrabold text-white mt-1">
                {Math.round(genreData.diversity_score * 100)}%
              </div>
              <p className="text-xs text-spotify-subtext mt-1">
                {genreData.diversity_score > 0.5 ? 'Gosto Eclético & Aberto' : 'Gosto Focado'}
              </p>
            </div>
          </div>

          {/* Charts Section */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Top 10 Genres Bar Chart */}
            <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
              <h3 className="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <TrendingUp className="w-5 h-5 text-spotify-green" />
                Top 8 Gêneros Mais Frequentes
              </h3>

              <div className="h-72 w-full">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart
                    data={genreData.top_genres.slice(0, 8)}
                    layout="vertical"
                    margin={{ top: 5, right: 30, left: 40, bottom: 5 }}
                  >
                    <XAxis type="number" stroke="#6a6a6a" fontSize={11} />
                    <YAxis
                      type="category"
                      dataKey="name"
                      stroke="#a7a7a7"
                      fontSize={11}
                      width={90}
                    />
                    <Tooltip
                      contentStyle={{
                        backgroundColor: '#181818',
                        borderColor: '#282828',
                        borderRadius: '12px',
                        color: '#fff',
                      }}
                      formatter={(value: any) => [`${value}% das preferências`, 'Frequência']}
                    />
                    <Bar dataKey="percentage" radius={[0, 8, 8, 0]}>
                      {genreData.top_genres.slice(0, 8).map((_, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Bar>
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </div>

            {/* Distribution Pie Chart */}
            <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6 flex flex-col justify-between">
              <h3 className="text-lg font-bold text-white mb-2 flex items-center gap-2">
                <Layers className="w-5 h-5 text-spotify-green" />
                Fatia de Concentração
              </h3>
              <p className="text-xs text-spotify-muted mb-4">
                Distribuição percentual entre seus principais gêneros musicais.
              </p>

              <div className="h-64 w-full flex items-center justify-center">
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie
                      data={genreData.top_genres.slice(0, 6)}
                      dataKey="percentage"
                      nameKey="name"
                      cx="50%"
                      cy="50%"
                      outerRadius={85}
                      innerRadius={45}
                      paddingAngle={4}
                      label={({ name, percent }: any) =>
                        `${name} (${(percent * 100).toFixed(0)}%)`
                      }
                      labelLine={false}
                    >
                      {genreData.top_genres.slice(0, 6).map((_, index) => (
                        <Cell key={`pie-cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                    <Tooltip
                      contentStyle={{
                        backgroundColor: '#181818',
                        borderColor: '#282828',
                        borderRadius: '12px',
                        color: '#fff',
                      }}
                    />
                  </PieChart>
                </ResponsiveContainer>
              </div>
            </div>
          </div>

          {/* Full Genre Rankings & Rare Genres */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {/* Top Genres List */}
            <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
              <h3 className="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <TrendingUp className="w-5 h-5 text-spotify-green" />
                Ranking de Gêneros Principais
              </h3>
              <div className="space-y-2.5">
                {genreData.top_genres.map((genre, idx) => (
                  <div
                    key={genre.name}
                    className="flex items-center justify-between p-3 rounded-xl bg-spotify-surface/40 hover:bg-spotify-surface transition-colors"
                  >
                    <div className="flex items-center gap-3">
                      <span className="w-5 text-xs font-bold text-spotify-subtext">{idx + 1}</span>
                      <span className="text-sm font-semibold text-white">{genre.name}</span>
                    </div>
                    <div className="flex items-center gap-3">
                      <span className="text-xs font-bold text-spotify-green">
                        {genre.percentage}%
                      </span>
                      <span className="text-xs text-spotify-subtext">({genre.count}x)</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Least Frequent Genres */}
            <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
              <h3 className="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <TrendingDown className="w-5 h-5 text-purple-400" />
                Gêneros Mais Raros no Seu Perfil
              </h3>
              <p className="text-xs text-spotify-muted mb-4">
                Subgêneros e nichos que aparecem de forma sutil no seu perfil musical.
              </p>
              <div className="space-y-2.5">
                {genreData.rare_genres.map((genre) => (
                  <div
                    key={genre.name}
                    className="flex items-center justify-between p-3 rounded-xl bg-spotify-surface/40 hover:bg-spotify-surface transition-colors border border-purple-500/10"
                  >
                    <span className="text-sm font-semibold text-white">{genre.name}</span>
                    <div className="flex items-center gap-3">
                      <span className="text-xs font-bold text-purple-300">
                        {genre.percentage}%
                      </span>
                      <span className="text-xs text-spotify-subtext">
                        ({genre.count} ocorrência)
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
