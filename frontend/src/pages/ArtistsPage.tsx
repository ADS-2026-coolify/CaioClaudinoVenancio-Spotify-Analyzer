import React, { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { TimeRangeSelector } from '@/components/ui/TimeRangeSelector';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import type { TimeRange } from '@/types';
import { Crown, ExternalLink, Mic2, TrendingUp, Users } from 'lucide-react';
import { motion } from 'framer-motion';

export const ArtistsPage: React.FC = () => {
  const { isDemo } = useAuth();
  const [timeRange, setTimeRange] = useState<TimeRange>('medium_term');

  const {
    data: artists = [],
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['artists', timeRange, isDemo],
    queryFn: () => api.getTopArtists(timeRange, isDemo),
  });

  return (
    <div className="space-y-8 pb-12">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
            <Mic2 className="w-4 h-4" />
            Análise de Artistas
          </div>
          <h1 className="text-3xl font-extrabold text-white mt-1">Seus Artistas Favoritos</h1>
          <p className="text-sm text-spotify-muted mt-1">
            Artistas mais escutados com base nos registros oficiais da API do Spotify.
          </p>
        </div>

        <TimeRangeSelector value={timeRange} onChange={setTimeRange} />
      </div>

      {isLoading ? (
        <CardSkeleton count={6} />
      ) : isError ? (
        <ErrorMessage onRetry={() => refetch()} />
      ) : artists.length === 0 ? (
        <div className="p-12 text-center text-spotify-muted">
          Nenhum artista registrado para este período.
        </div>
      ) : (
        <div className="space-y-6">
          {/* Top 3 Podium (Desktop) */}
          {artists.length >= 3 && (
            <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
              {/* #2 Silver */}
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: 0.1 }}
                className="rounded-3xl border border-spotify-border bg-spotify-dark/70 p-6 flex flex-col items-center text-center relative order-2 md:order-1"
              >
                <div className="w-8 h-8 rounded-full bg-slate-400 text-black font-extrabold flex items-center justify-center text-xs mb-3 shadow">
                  #2
                </div>
                {artists[1].image_url && (
                  <img
                    src={artists[1].image_url}
                    alt={artists[1].name}
                    className="w-28 h-28 rounded-2xl object-cover mb-4 ring-2 ring-slate-400/40 shadow-lg"
                  />
                )}
                <h3 className="text-lg font-bold text-white truncate w-full">{artists[1].name}</h3>
                <p className="text-xs text-spotify-muted capitalize truncate w-full mt-1">
                  {artists[1].genres.slice(0, 2).join(' • ') || 'Música'}
                </p>
                <div className="mt-4 flex items-center gap-2">
                  <span className="text-xs font-semibold px-2.5 py-1 rounded-full bg-spotify-surface border border-spotify-border text-spotify-muted">
                    {artists[1].popularity}% popularidade
                  </span>
                  {artists[1].spotify_url && (
                    <a
                      href={artists[1].spotify_url}
                      target="_blank"
                      rel="noreferrer"
                      className="p-1.5 rounded-full bg-spotify-surface hover:text-spotify-green text-spotify-muted transition-colors"
                    >
                      <ExternalLink className="w-3.5 h-3.5" />
                    </a>
                  )}
                </div>
              </motion.div>

              {/* #1 Gold Champion */}
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="rounded-3xl border border-amber-500/40 bg-gradient-to-b from-amber-500/10 via-spotify-dark to-spotify-dark p-6 flex flex-col items-center text-center relative order-1 md:order-2 md:-translate-y-2 glow-subtle"
              >
                <div className="w-9 h-9 rounded-full bg-amber-400 text-black font-black flex items-center justify-center text-sm mb-3 shadow-lg shadow-amber-400/20">
                  <Crown className="w-5 h-5 fill-current" />
                </div>
                {artists[0].image_url && (
                  <img
                    src={artists[0].image_url}
                    alt={artists[0].name}
                    className="w-32 h-32 rounded-2xl object-cover mb-4 ring-4 ring-amber-400/50 shadow-2xl"
                  />
                )}
                <h3 className="text-xl font-extrabold text-white truncate w-full">
                  {artists[0].name}
                </h3>
                <p className="text-xs text-amber-300/80 capitalize truncate w-full mt-1">
                  {artists[0].genres.slice(0, 3).join(' • ') || 'Música'}
                </p>
                <div className="mt-4 flex items-center gap-2">
                  <span className="text-xs font-bold px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300">
                    #1 Artista Favorito
                  </span>
                  {artists[0].spotify_url && (
                    <a
                      href={artists[0].spotify_url}
                      target="_blank"
                      rel="noreferrer"
                      className="p-1.5 rounded-full bg-spotify-surface hover:text-amber-400 text-spotify-muted transition-colors"
                    >
                      <ExternalLink className="w-3.5 h-3.5" />
                    </a>
                  )}
                </div>
              </motion.div>

              {/* #3 Bronze */}
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: 0.2 }}
                className="rounded-3xl border border-spotify-border bg-spotify-dark/70 p-6 flex flex-col items-center text-center relative order-3"
              >
                <div className="w-8 h-8 rounded-full bg-amber-700 text-white font-extrabold flex items-center justify-center text-xs mb-3 shadow">
                  #3
                </div>
                {artists[2].image_url && (
                  <img
                    src={artists[2].image_url}
                    alt={artists[2].name}
                    className="w-28 h-28 rounded-2xl object-cover mb-4 ring-2 ring-amber-700/40 shadow-lg"
                  />
                )}
                <h3 className="text-lg font-bold text-white truncate w-full">{artists[2].name}</h3>
                <p className="text-xs text-spotify-muted capitalize truncate w-full mt-1">
                  {artists[2].genres.slice(0, 2).join(' • ') || 'Música'}
                </p>
                <div className="mt-4 flex items-center gap-2">
                  <span className="text-xs font-semibold px-2.5 py-1 rounded-full bg-spotify-surface border border-spotify-border text-spotify-muted">
                    {artists[2].popularity}% popularidade
                  </span>
                  {artists[2].spotify_url && (
                    <a
                      href={artists[2].spotify_url}
                      target="_blank"
                      rel="noreferrer"
                      className="p-1.5 rounded-full bg-spotify-surface hover:text-spotify-green text-spotify-muted transition-colors"
                    >
                      <ExternalLink className="w-3.5 h-3.5" />
                    </a>
                  )}
                </div>
              </motion.div>
            </div>
          )}

          {/* Full Artist Ranking List */}
          <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6">
            <h3 className="text-lg font-bold text-white mb-5 flex items-center gap-2">
              <TrendingUp className="w-5 h-5 text-spotify-green" />
              Ranking Geral de Artistas ({artists.length})
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-3.5">
              {artists.map((artist) => (
                <div
                  key={artist.id}
                  className="flex items-center justify-between p-3.5 rounded-2xl bg-spotify-surface/40 hover:bg-spotify-surface transition-all group border border-transparent hover:border-spotify-border"
                >
                  <div className="flex items-center gap-4 min-w-0">
                    <span className="w-6 text-center font-extrabold text-sm text-spotify-subtext group-hover:text-spotify-green transition-colors">
                      {artist.rank}
                    </span>

                    {artist.image_url ? (
                      <img
                        src={artist.image_url}
                        alt={artist.name}
                        className="w-12 h-12 rounded-xl object-cover ring-1 ring-spotify-border shrink-0"
                      />
                    ) : (
                      <div className="w-12 h-12 rounded-xl bg-spotify-surface flex items-center justify-center shrink-0">
                        <Mic2 className="w-5 h-5 text-spotify-muted" />
                      </div>
                    )}

                    <div className="min-w-0">
                      <h4 className="font-bold text-sm text-white truncate group-hover:text-spotify-green transition-colors">
                        {artist.name}
                      </h4>
                      <p className="text-xs text-spotify-muted truncate capitalize mt-0.5">
                        {artist.genres.slice(0, 3).join(', ') || 'Gênero variado'}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 shrink-0 pl-3">
                    {/* Popularity indicator bar */}
                    <div className="hidden sm:flex flex-col items-end gap-1">
                      <span className="text-[10px] font-semibold text-spotify-subtext">
                        {artist.popularity}/100
                      </span>
                      <div className="w-16 h-1.5 bg-spotify-surface rounded-full overflow-hidden">
                        <div
                          className="h-full bg-spotify-green rounded-full"
                          style={{ width: `${artist.popularity}%` }}
                        />
                      </div>
                    </div>

                    {artist.spotify_url && (
                      <a
                        href={artist.spotify_url}
                        target="_blank"
                        rel="noreferrer"
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

