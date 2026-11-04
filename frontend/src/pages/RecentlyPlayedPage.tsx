import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import { Clock, Disc, ExternalLink, History, Info, Music, Play } from 'lucide-react';

export const RecentlyPlayedPage: React.FC = () => {
  const { isDemo } = useAuth();

  const {
    data,
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['recently-played', isDemo],
    queryFn: () => api.getRecentlyPlayed(isDemo),
  });

  const tracks = data?.tracks || [];

  return (
    <div className="space-y-8 pb-12">
      {/* Header */}
      <div>
        <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
          <History className="w-4 h-4" />
          Histórico Recente
        </div>
        <h1 className="text-3xl font-extrabold text-white mt-1">Últimas Faixas Tocadas</h1>
        <p className="text-sm text-spotify-muted mt-1">
          Acompanhe suas sessões sonoras mais recentes em tempo real.
        </p>
      </div>

      {/* Official Spotify API Window Notice */}
      <div className="p-4 rounded-2xl border border-spotify-border/80 bg-spotify-dark/80 flex items-start gap-3">
        <Info className="w-5 h-5 text-spotify-green shrink-0 mt-0.5" />
        <div className="text-xs text-spotify-muted leading-relaxed">
          <strong className="text-white">Janela limitada da Spotify Web API:</strong> A API oficial
          do Spotify disponibiliza exclusivamente uma janela das reproduções mais recentes
          (aproximadamente as últimas 50 músicas tocadas no seu player), e{' '}
          <strong>não o histórico vitalício completo da sua conta</strong>.
        </div>
      </div>

      {isLoading ? (
        <CardSkeleton count={4} />
      ) : isError ? (
        <ErrorMessage onRetry={() => refetch()} />
      ) : tracks.length === 0 ? (
        <div className="p-12 text-center text-spotify-muted">
          Nenhuma reprodução recente registrada no momento. Dê o play no Spotify e volte aqui!
        </div>
      ) : (
        <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-4 sm:p-6 overflow-hidden">
          <h3 className="text-lg font-bold text-white mb-5 flex items-center gap-2">
            <History className="w-5 h-5 text-spotify-green" />
            Linha do Tempo de Reproduções ({tracks.length})
          </h3>

          <div className="divide-y divide-spotify-border/40">
            {tracks.map((item, index) => (
              <div
                key={`${item.id}-${index}`}
                className="flex items-center justify-between py-3.5 px-2 rounded-xl hover:bg-spotify-surface/40 transition-colors group"
              >
                <div className="flex items-center gap-4 min-w-0">
                  {item.image_url ? (
                    <img
                      src={item.image_url}
                      alt={item.name}
                      className="w-12 h-12 rounded-xl object-cover ring-1 ring-spotify-border shrink-0"
                    />
                  ) : (
                    <div className="w-12 h-12 rounded-xl bg-spotify-surface flex items-center justify-center shrink-0">
                      <Music className="w-5 h-5 text-spotify-muted" />
                    </div>
                  )}

                  <div className="min-w-0">
                    <h4 className="font-bold text-sm text-white truncate group-hover:text-spotify-green transition-colors">
                      {item.name}
                    </h4>
                    <p className="text-xs text-spotify-muted truncate mt-0.5">{item.artists}</p>
                    <p className="text-[11px] text-spotify-subtext truncate hidden sm:block">
                      {item.album}
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-3 sm:gap-6 shrink-0 pl-3">
                  {/* Played At Relative */}
                  {item.played_at_human && (
                    <span className="inline-flex items-center gap-1.5 text-xs text-spotify-green font-medium">
                      <Clock className="w-3.5 h-3.5" />
                      {item.played_at_human}
                    </span>
                  )}

                  {item.spotify_url && (
                    <a
                      href={item.spotify_url}
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
      )}
    </div>
  );
};

