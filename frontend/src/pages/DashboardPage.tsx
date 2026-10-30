import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { StatCard } from '@/components/ui/StatCard';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import {
  Calendar,
  Compass,
  Crown,
  ExternalLink,
  Flame,
  ListMusic,
  Mic2,
  Music,
  Play,
  Radio,
  Sparkles,
  User as UserIcon,
} from 'lucide-react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';

export const DashboardPage: React.FC = () => {
  const { isDemo } = useAuth();

  const {
    data: dashboard,
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['dashboard', isDemo],
    queryFn: () => api.getDashboard(isDemo),
  });

  if (isLoading) {
    return (
      <div className="space-y-8 animate-fadeIn">
        <div className="h-20 w-80 bg-spotify-surface rounded-2xl animate-pulse" />
        <CardSkeleton count={4} />
        <CardSkeleton count={3} />
      </div>
    );
  }

  if (isError || !dashboard) {
    return <ErrorMessage onRetry={() => refetch()} />;
  }

  const { user, greeting, stats, top_artists_preview, top_tracks_preview, insights_preview } =
    dashboard;

  return (
    <div className="space-y-8 pb-12">
      {/* Header Profile Hero */}
      <motion.div
        initial={{ opacity: 0, y: 15 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="relative overflow-hidden rounded-3xl border border-spotify-border bg-gradient-to-r from-spotify-dark via-spotify-black to-spotify-surface/40 p-6 sm:p-8"
      >
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
          <div className="flex items-center gap-4 sm:gap-6">
            {user.avatar_url ? (
              <img
                src={user.avatar_url}
                alt={user.display_name}
                className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-2 ring-spotify-green shadow-lg shadow-spotify-green/20"
              />
            ) : (
              <div className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-spotify-surface flex items-center justify-center text-spotify-muted ring-2 ring-spotify-border">
                <UserIcon className="w-8 h-8" />
              </div>
            )}

            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-bold uppercase tracking-wider text-spotify-green">
                  {greeting},
                </span>
                {user.country && (
                  <span className="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-spotify-surface border border-spotify-border text-spotify-muted">
                    {user.country}
                  </span>
                )}
              </div>
              <h1 className="text-2xl sm:text-4xl font-extrabold text-white mt-0.5 tracking-tight">
                {user.display_name || user.name}
              </h1>
              <p className="text-xs sm:text-sm text-spotify-muted mt-1">
                Aqui está o panorama completo dos seus hábitos e preferências musicais.
              </p>
            </div>
          </div>

          <Link
            to="/playlists"
            className="w-full sm:w-auto px-5 py-3 rounded-full bg-spotify-green hover:bg-spotify-green-light text-black font-bold text-sm flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md shadow-spotify-green/20"
          >
            <ListMusic className="w-4 h-4" />
            Criar Playlist por Clima
          </Link>
        </div>
      </motion.div>

      {/* Key Metric Indicator Cards */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
        <StatCard
          title="Artistas Analisados"
          value={stats.total_artists_analyzed}
          subtitle="Amostra de catálogo"
          icon={Mic2}
        />
        <StatCard
          title="Músicas Analisadas"
          value={stats.total_tracks_analyzed}
          subtitle="Top faixas identificadas"
          icon={Music}
        />
        <StatCard
          title="Gênero Predominante"
          value={stats.predominant_genre}
          subtitle="Maior frequência de artistas"
          icon={Compass}
          highlight
        />
        <StatCard
          title="Sua Década"
          value={stats.predominant_decade}
          subtitle="Lançamentos predominantes"
          icon={Calendar}
        />
      </div>

      {/* Spotlight: Favorite Artist & Top Track Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        {/* Favorite Artist Spotlight */}
        {stats.favorite_artist && (
          <motion.div
            initial={{ opacity: 0, scale: 0.98 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.4, delay: 0.1 }}
            className="rounded-3xl border border-spotify-border bg-gradient-to-br from-amber-500/10 via-spotify-dark to-spotify-black p-6 relative overflow-hidden"
          >
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center gap-2">
                <Crown className="w-5 h-5 text-amber-400" />
                <span className="text-xs font-bold uppercase tracking-wider text-amber-400">
                  Artista Favorito
                </span>
              </div>
              <span className="text-xs text-spotify-muted">#1 mais escutado</span>
            </div>

            <div className="flex items-center gap-5">
              {stats.favorite_artist.image_url ? (
                <img
                  src={stats.favorite_artist.image_url}
                  alt={stats.favorite_artist.name}
                  className="w-20 h-20 rounded-2xl object-cover shadow-xl ring-2 ring-amber-500/30"
                />
              ) : (
                <div className="w-20 h-20 rounded-2xl bg-spotify-surface flex items-center justify-center">
                  <Mic2 className="w-8 h-8 text-spotify-muted" />
                </div>
              )}

              <div className="flex-1 min-w-0">
                <h3 className="text-xl font-extrabold text-white truncate">
                  {stats.favorite_artist.name}
                </h3>
                <div className="flex flex-wrap gap-1.5 mt-2">
                  {stats.favorite_artist.genres.slice(0, 3).map((g) => (
                    <span
                      key={g}
                      className="text-[11px] font-medium px-2 py-0.5 rounded-full bg-spotify-surface border border-spotify-border text-spotify-muted capitalize"
                    >
                      {g}
                    </span>
                  ))}
                </div>
              </div>
            </div>

            <div className="mt-5 pt-4 border-t border-spotify-border/60 flex items-center justify-between">
              <span className="text-xs text-spotify-muted">
                Popularidade global: <strong>{stats.favorite_artist.popularity}/100</strong>
              </span>
              <Link
                to="/analytics/artists"
                className="text-xs font-semibold text-spotify-green hover:underline flex items-center gap-1"
              >
                Ver ranking completo →
              </Link>
            </div>
          </motion.div>
        )}

        {/* Most Recurring Track Spotlight */}
        {stats.top_track && (
          <motion.div
            initial={{ opacity: 0, scale: 0.98 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.4, delay: 0.2 }}
            className="rounded-3xl border border-spotify-border bg-gradient-to-br from-spotify-green/10 via-spotify-dark to-spotify-black p-6 relative overflow-hidden"
          >
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center gap-2">
                <Flame className="w-5 h-5 text-spotify-green" />
                <span className="text-xs font-bold uppercase tracking-wider text-spotify-green">
                  Música Mais Recorrente
                </span>
              </div>
              <span className="text-xs text-spotify-muted">#1 faixa do seu top</span>
            </div>

            <div className="flex items-center gap-5">
              {stats.top_track.image_url ? (
                <img
                  src={stats.top_track.image_url}
                  alt={stats.top_track.name}
                  className="w-20 h-20 rounded-2xl object-cover shadow-xl ring-2 ring-spotify-green/30"
                />
              ) : (
                <div className="w-20 h-20 rounded-2xl bg-spotify-surface flex items-center justify-center">
                  <Music className="w-8 h-8 text-spotify-muted" />
                </div>
              )}

              <div className="flex-1 min-w-0">
                <h3 className="text-xl font-extrabold text-white truncate">
                  {stats.top_track.name}
                </h3>
                <p className="text-sm text-spotify-muted truncate mt-0.5">
                  {stats.top_track.artist}
                </p>
                <p className="text-xs text-spotify-subtext truncate mt-0.5">
                  {stats.top_track.album}
                </p>
              </div>
            </div>

            <div className="mt-5 pt-4 border-t border-spotify-border/60 flex items-center justify-between">
              {stats.top_track.spotify_url ? (
                <a
                  href={stats.top_track.spotify_url}
                  target="_blank"
                  rel="noreferrer"
                  className="inline-flex items-center gap-1.5 text-xs font-bold text-black px-3.5 py-1.5 rounded-full bg-spotify-green hover:bg-spotify-green-light transition-colors"
                >
                  <Play className="w-3.5 h-3.5 fill-current" />
                  Ouvir no Spotify
                </a>
              ) : (
                <span className="text-xs text-spotify-muted">Faixa favorita</span>
              )}
              <Link
                to="/analytics/tracks"
                className="text-xs font-semibold text-spotify-green hover:underline flex items-center gap-1"
              >
                Ver top músicas →
              </Link>
            </div>
          </motion.div>
        )}
      </div>

      {/* Quick Previews: Top Artists & Top Tracks */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Top 5 Artists Preview */}
        <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
          <div className="flex items-center justify-between mb-5">
            <h3 className="text-lg font-bold text-white flex items-center gap-2">
              <Mic2 className="w-5 h-5 text-spotify-green" />
              Seus Artistas Mais Ouvidos
            </h3>
            <Link
              to="/analytics/artists"
              className="text-xs font-semibold text-spotify-green hover:underline"
            >
              Ver todos (50) →
            </Link>
          </div>

          <div className="space-y-3">
            {top_artists_preview.map((artist) => (
              <div
                key={artist.id}
                className="flex items-center justify-between p-3 rounded-2xl bg-spotify-surface/40 hover:bg-spotify-surface transition-colors"
              >
                <div className="flex items-center gap-3.5 min-w-0">
                  <span className="w-6 text-center text-sm font-extrabold text-spotify-subtext">
                    {artist.rank}
                  </span>
                  {artist.image_url ? (
                    <img
                      src={artist.image_url}
                      alt={artist.name}
                      className="w-11 h-11 rounded-xl object-cover"
                    />
                  ) : (
                    <div className="w-11 h-11 rounded-xl bg-spotify-surface flex items-center justify-center text-spotify-muted">
                      <Mic2 className="w-5 h-5" />
                    </div>
                  )}
                  <div className="min-w-0">
                    <h4 className="text-sm font-bold text-white truncate">{artist.name}</h4>
                    <p className="text-xs text-spotify-muted truncate capitalize">
                      {artist.genres.slice(0, 2).join(' • ') || 'Música'}
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-3 shrink-0">
                  <span className="text-xs font-semibold text-spotify-muted">
                    {artist.popularity}% pop
                  </span>
                  {artist.spotify_url && (
                    <a
                      href={artist.spotify_url}
                      target="_blank"
                      rel="noreferrer"
                      className="text-spotify-muted hover:text-spotify-green transition-colors"
                    >
                      <ExternalLink className="w-4 h-4" />
                    </a>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Top 5 Tracks Preview */}
        <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
          <div className="flex items-center justify-between mb-5">
            <h3 className="text-lg font-bold text-white flex items-center gap-2">
              <Music className="w-5 h-5 text-spotify-green" />
              Músicas Mais Recorrentes
            </h3>
            <Link
              to="/analytics/tracks"
              className="text-xs font-semibold text-spotify-green hover:underline"
            >
              Ver todas (50) →
            </Link>
          </div>

          <div className="space-y-3">
            {top_tracks_preview.map((track) => (
              <div
                key={track.id}
                className="flex items-center justify-between p-3 rounded-2xl bg-spotify-surface/40 hover:bg-spotify-surface transition-colors"
              >
                <div className="flex items-center gap-3.5 min-w-0">
                  <span className="w-6 text-center text-sm font-extrabold text-spotify-subtext">
                    {track.rank}
                  </span>
                  {track.image_url ? (
                    <img
                      src={track.image_url}
                      alt={track.name}
                      className="w-11 h-11 rounded-xl object-cover"
                    />
                  ) : (
                    <div className="w-11 h-11 rounded-xl bg-spotify-surface flex items-center justify-center text-spotify-muted">
                      <Music className="w-5 h-5" />
                    </div>
                  )}
                  <div className="min-w-0">
                    <h4 className="text-sm font-bold text-white truncate">{track.name}</h4>
                    <p className="text-xs text-spotify-muted truncate">{track.artists}</p>
                  </div>
                </div>

                <div className="flex items-center gap-3 shrink-0">
                  {track.release_date && (
                    <span className="text-[11px] font-semibold px-2 py-0.5 rounded bg-spotify-surface text-spotify-muted hidden sm:inline-block">
                      {track.release_date.slice(0, 4)}
                    </span>
                  )}
                  {track.spotify_url && (
                    <a
                      href={track.spotify_url}
                      target="_blank"
                      rel="noreferrer"
                      className="text-spotify-muted hover:text-spotify-green transition-colors"
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

      {/* Quick Insights Highlights */}
      {insights_preview.length > 0 && (
        <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
          <div className="flex items-center justify-between mb-5">
            <h3 className="text-lg font-bold text-white flex items-center gap-2">
              <Sparkles className="w-5 h-5 text-spotify-green" />
              Insights do Seu Perfil Musical
            </h3>
            <Link
              to="/analytics/insights"
              className="text-xs font-semibold text-spotify-green hover:underline"
            >
              Ver todos os insights →
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {insights_preview.map((insight, idx) => (
              <div
                key={idx}
                className="p-5 rounded-2xl bg-spotify-surface/40 border border-spotify-border/60 hover:border-spotify-green/40 transition-all"
              >
                <div className="text-[10px] font-bold uppercase tracking-wider text-spotify-green mb-1.5">
                  {insight.tag}
                </div>
                <h4 className="text-base font-bold text-white mb-2">{insight.title}</h4>
                <p className="text-xs text-spotify-muted leading-relaxed">
                  {insight.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};
