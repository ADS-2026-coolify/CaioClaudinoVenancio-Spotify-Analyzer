import React, { useState } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import type { GeneratedPlaylistPreview, MoodCategory } from '@/types';
import {
  CheckCircle2,
  Clock,
  ExternalLink,
  History,
  Info,
  ListMusic,
  Lock,
  Music,
  Play,
  Plus,
  RefreshCw,
  Sparkles,
  Unlock,
} from 'lucide-react';
import { motion } from 'framer-motion';

const MOODS: { value: MoodCategory; label: string; emoji: string; desc: string }[] = [
  { value: 'rain', label: 'Chuva', emoji: '🌧️', desc: 'Acústico, indie e lo-fi melancólico' },
  { value: 'party', label: 'Festa', emoji: '🎉', desc: 'Pop, dance, batidas energéticas' },
  { value: 'focus', label: 'Foco', emoji: '🎯', desc: 'Instrumental, ambient e concentração' },
  { value: 'relax', label: 'Relaxar', emoji: '😌', desc: 'Chill, bossa nova e jazz suave' },
  { value: 'nostalgia', label: 'Nostalgia', emoji: '💭', desc: 'Grandes clássicos e memórias' },
  { value: 'workout', label: 'Treino', emoji: '💪', desc: 'Alta energia, hip-hop e rock pesado' },
  { value: 'road_trip', label: 'Viagem', emoji: '🚗', desc: 'Rock na estrada e pop vibrante' },
  { value: 'late_night', label: 'Madrugada', emoji: '🌙', desc: 'R&B, soul e vibes noturnas' },
  { value: 'romantic', label: 'Romântico', emoji: '❤️', desc: 'Baladas, love songs e aconchego' },
];

export const PlaylistsPage: React.FC = () => {
  const { isDemo, loginWithSpotify } = useAuth();
  const queryClient = useQueryClient();

  // State
  const [selectedMood, setSelectedMood] = useState<MoodCategory>('relax');
  const [trackCount, setTrackCount] = useState<number>(20);
  const [playlistPreview, setPlaylistPreview] = useState<GeneratedPlaylistPreview | null>(null);

  // Form customizer state
  const [playlistName, setPlaylistName] = useState<string>('');
  const [playlistDesc, setPlaylistDesc] = useState<string>('');
  const [isPublic, setIsPublic] = useState<boolean>(true);
  const [createdUrl, setCreatedUrl] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  // Fetch past generated playlists
  const { data: pastPlaylists = [] } = useQuery({
    queryKey: ['user-playlists', isDemo],
    queryFn: () => (isDemo ? Promise.resolve([]) : api.getUserPlaylists()),
    enabled: !isDemo,
  });

  // Mutation: Generate Preview
  const generateMutation = useMutation({
    mutationFn: () => api.generatePlaylistPreview(selectedMood, trackCount, isDemo),
    onSuccess: (data) => {
      setPlaylistPreview(data);
      setPlaylistName(`${data.mood_emoji} Momento ${data.mood_label}`);
      setPlaylistDesc(
        `Playlist sob medida para momento ${data.mood_label}, com base no meu Spotify Profile Analyzer.`
      );
      setCreatedUrl(null);
      setSuccessMessage(null);
    },
  });

  // Mutation: Create Playlist on Spotify
  const createMutation = useMutation({
    mutationFn: async () => {
      if (!playlistPreview) return;
      const uris = playlistPreview.tracks.map((t) => t.uri);
      return api.createSpotifyPlaylist({
        name: playlistName,
        description: playlistDesc,
        is_public: isPublic,
        mood: selectedMood,
        track_uris: uris,
      });
    },
    onSuccess: (created) => {
      if (created) {
        setCreatedUrl(created.spotify_url || null);
        setSuccessMessage('Playlist salva na sua biblioteca do Spotify com sucesso!');
        queryClient.invalidateQueries({ queryKey: ['user-playlists'] });
      }
    },
  });

  const handleCreate = () => {
    if (isDemo) {
      setCreatedUrl('https://open.spotify.com');
      setSuccessMessage('No Modo Demonstração a playlist é simulada com sucesso!');
      return;
    }
    createMutation.mutate();
  };

  return (
    <div className="space-y-8 pb-12">
      {/* Header */}
      <div>
        <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
          <ListMusic className="w-4 h-4" />
          Gerador Inteligente
        </div>
        <h1 className="text-3xl font-extrabold text-white mt-1">Monte uma Playlist pro seu Momento</h1>
        <p className="text-sm text-spotify-muted mt-1">
          Combine suas preferências musicais com climas e contextos sem depender de recomendações genéricas.
        </p>
      </div>

      {/* Heuristic Transparency Alert */}
      <div className="p-4 rounded-2xl border border-spotify-border/80 bg-spotify-dark/80 flex items-start gap-3">
        <Info className="w-5 h-5 text-spotify-green shrink-0 mt-0.5" />
        <div className="text-xs text-spotify-muted leading-relaxed">
          <strong className="text-white">Como funciona nosso gerador:</strong> O endpoint antigo de
          recomendações do Spotify foi descontinuado para novos aplicativos. Implementamos um
          algoritmo próprio que cruza os gêneros dos seus artistas favoritos com termos de busca
          contextuais e níveis de popularidade para curar faixas sob medida.
        </div>
      </div>

      {/* Step 1: Select Mood */}
      <div className="space-y-4">
        <h3 className="text-lg font-bold text-white flex items-center gap-2">
          <span className="w-6 h-6 rounded-full bg-spotify-green text-black text-xs font-black flex items-center justify-center">
            1
          </span>
          Escolha o Clima Musical
        </h3>

        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-3.5">
          {MOODS.map((mood) => {
            const isSelected = selectedMood === mood.value;
            return (
              <button
                key={mood.value}
                onClick={() => setSelectedMood(mood.value)}
                className={`p-4 rounded-2xl border text-left transition-all ${
                  isSelected
                    ? 'border-spotify-green bg-gradient-to-br from-spotify-green/20 via-spotify-dark to-spotify-dark ring-2 ring-spotify-green/40 shadow-lg glow-subtle'
                    : 'border-spotify-border bg-spotify-dark/70 hover:bg-spotify-surface hover:border-spotify-border-light'
                }`}
              >
                <div className="text-2xl mb-2">{mood.emoji}</div>
                <div className="text-sm font-bold text-white">{mood.label}</div>
                <div className="text-[11px] text-spotify-muted mt-0.5 line-clamp-1">
                  {mood.desc}
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* Step 2: Track Count & Generate Action */}
      <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
          <h3 className="text-base font-bold text-white flex items-center gap-2">
            <span className="w-6 h-6 rounded-full bg-spotify-green text-black text-xs font-black flex items-center justify-center">
              2
            </span>
            Quantidade de Músicas
          </h3>
          <p className="text-xs text-spotify-muted mt-1">
            Selecione quantas faixas deseja na sua playlist sob medida.
          </p>

          <div className="flex gap-2 mt-3">
            {[10, 20, 30, 50].map((count) => (
              <button
                key={count}
                onClick={() => setTrackCount(count)}
                className={`px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all ${
                  trackCount === count
                    ? 'bg-spotify-green text-black'
                    : 'bg-spotify-surface text-spotify-muted hover:text-white border border-spotify-border'
                }`}
              >
                {count} faixas
              </button>
            ))}
          </div>
        </div>

        <button
          onClick={() => generateMutation.mutate()}
          disabled={generateMutation.isPending}
          className="px-8 py-3.5 rounded-full bg-spotify-green hover:bg-spotify-green-light disabled:opacity-50 text-black font-extrabold text-sm flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-md shadow-spotify-green/20"
        >
          {generateMutation.isPending ? (
            <>
              <RefreshCw className="w-4 h-4 animate-spin" />
              Analisando & Buscando...
            </>
          ) : (
            <>
              <Sparkles className="w-4 h-4 fill-current" />
              Gerar Prévia da Playlist
            </>
          )}
        </button>
      </div>

      {/* Step 3: Playlist Preview & Saving */}
      {playlistPreview && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.4 }}
          className="rounded-3xl border border-spotify-green/40 bg-gradient-to-b from-spotify-dark via-spotify-black to-spotify-dark p-6 sm:p-8 space-y-6 glow-subtle"
        >
          {/* Header of preview */}
          <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-spotify-border/60 gap-4">
            <div className="flex items-center gap-3">
              <span className="text-4xl">{playlistPreview.mood_emoji}</span>
              <div>
                <span className="text-[11px] font-extrabold uppercase tracking-wider text-spotify-green">
                  Prévia Gerada com Sucesso
                </span>
                <h3 className="text-xl sm:text-2xl font-black text-white">
                  {playlistPreview.total_tracks} Músicas Selecionadas ({playlistPreview.estimated_duration_minutes} min)
                </h3>
              </div>
            </div>

            <div className="flex items-center gap-3 w-full sm:w-auto">
              <button
                onClick={() => generateMutation.mutate()}
                className="px-4 py-2 rounded-full bg-spotify-surface hover:bg-spotify-surface-hover text-xs font-semibold text-white border border-spotify-border transition-colors flex items-center gap-1.5"
              >
                <RefreshCw className="w-3.5 h-3.5" />
                Regenerar
              </button>
            </div>
          </div>

          {/* Success Banner if created */}
          {successMessage && (
            <div className="p-4 rounded-2xl bg-spotify-green/20 border border-spotify-green text-spotify-green flex flex-col sm:flex-row items-center justify-between gap-3">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-5 h-5 shrink-0" />
                <span className="text-sm font-bold text-white">{successMessage}</span>
              </div>
              {createdUrl && (
                <a
                  href={createdUrl}
                  target="_blank"
                  rel="noreferrer"
                  className="px-4 py-2 rounded-full bg-spotify-green text-black font-extrabold text-xs flex items-center gap-1.5 shadow"
                >
                  <Play className="w-3.5 h-3.5 fill-current" />
                  Abrir Playlist no Spotify
                  <ExternalLink className="w-3.5 h-3.5" />
                </a>
              )}
            </div>
          )}

          {/* Customization inputs */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-spotify-surface/40 border border-spotify-border/60">
            <div className="space-y-1">
              <label className="text-xs font-bold uppercase tracking-wider text-spotify-muted">
                Nome da Playlist
              </label>
              <input
                type="text"
                value={playlistName}
                onChange={(e) => setPlaylistName(e.target.value)}
                className="w-full px-3 py-2 rounded-xl bg-spotify-surface border border-spotify-border text-sm text-white focus:outline-none focus:border-spotify-green"
              />
            </div>

            <div className="space-y-1">
              <label className="text-xs font-bold uppercase tracking-wider text-spotify-muted">
                Descrição
              </label>
              <input
                type="text"
                value={playlistDesc}
                onChange={(e) => setPlaylistDesc(e.target.value)}
                className="w-full px-3 py-2 rounded-xl bg-spotify-surface border border-spotify-border text-sm text-white focus:outline-none focus:border-spotify-green"
              />
            </div>

            <div className="space-y-1 flex flex-col justify-end">
              <div className="flex items-center justify-between p-2 rounded-xl bg-spotify-surface border border-spotify-border">
                <span className="text-xs font-medium text-white flex items-center gap-1.5">
                  {isPublic ? <Unlock className="w-3.5 h-3.5 text-spotify-green" /> : <Lock className="w-3.5 h-3.5 text-spotify-muted" />}
                  {isPublic ? 'Playlist Pública' : 'Playlist Privada'}
                </span>
                <button
                  type="button"
                  onClick={() => setIsPublic((prev) => !prev)}
                  className={`w-9 h-5 rounded-full p-0.5 transition-colors ${
                    isPublic ? 'bg-spotify-green' : 'bg-spotify-subtext'
                  }`}
                >
                  <div
                    className={`w-4 h-4 rounded-full bg-black transition-transform ${
                      isPublic ? 'translate-x-4' : 'translate-x-0'
                    }`}
                  />
                </button>
              </div>
            </div>
          </div>

          {/* Action button to save */}
          <div className="flex justify-end">
            <button
              onClick={handleCreate}
              disabled={createMutation.isPending || !playlistName.trim()}
              className="w-full sm:w-auto px-8 py-3.5 rounded-full bg-spotify-green hover:bg-spotify-green-light disabled:opacity-50 text-black font-extrabold text-sm flex items-center justify-center gap-2 shadow-lg shadow-spotify-green/25 active:scale-95"
            >
              {createMutation.isPending ? (
                <>
                  <RefreshCw className="w-4 h-4 animate-spin" />
                  Salvando na sua conta...
                </>
              ) : (
                <>
                  <Plus className="w-4 h-4 stroke-[3]" />
                  Salvar Playlist no Meu Spotify
                </>
              )}
            </button>
          </div>

          {/* Track List Preview */}
          <div className="divide-y divide-spotify-border/40">
            {playlistPreview.tracks.map((track, index) => (
              <div
                key={track.id}
                className="flex items-center justify-between py-3 px-2 rounded-xl hover:bg-spotify-surface/40 transition-colors"
              >
                <div className="flex items-center gap-3.5 min-w-0">
                  <span className="w-6 text-center text-xs font-bold text-spotify-subtext">
                    {index + 1}
                  </span>

                  {track.image_url ? (
                    <img
                      src={track.image_url}
                      alt={track.name}
                      className="w-11 h-11 rounded-xl object-cover shrink-0"
                    />
                  ) : (
                    <div className="w-11 h-11 rounded-xl bg-spotify-surface flex items-center justify-center shrink-0">
                      <Music className="w-5 h-5 text-spotify-muted" />
                    </div>
                  )}

                  <div className="min-w-0">
                    <div className="flex items-center gap-2">
                      <h4 className="font-bold text-sm text-white truncate">{track.name}</h4>
                      {track.is_favorite_artist && (
                        <span className="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 shrink-0">
                          Seu Favorito
                        </span>
                      )}
                    </div>
                    <p className="text-xs text-spotify-muted truncate mt-0.5">{track.artists}</p>
                  </div>
                </div>

                <div className="flex items-center gap-3 shrink-0">
                  <span className="text-xs text-spotify-subtext font-mono">
                    {track.duration_formatted}
                  </span>
                  {track.spotify_url && (
                    <a
                      href={track.spotify_url}
                      target="_blank"
                      rel="noreferrer"
                      className="text-spotify-muted hover:text-spotify-green"
                    >
                      <ExternalLink className="w-4 h-4" />
                    </a>
                  )}
                </div>
              </div>
            ))}
          </div>
        </motion.div>
      )}

      {/* Past Created Playlists */}
      {pastPlaylists.length > 0 && (
        <div className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6 space-y-4">
          <h3 className="text-lg font-bold text-white flex items-center gap-2">
            <History className="w-5 h-5 text-spotify-green" />
            Playlists Criadas por Você ({pastPlaylists.length})
          </h3>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {pastPlaylists.map((pl) => (
              <div
                key={pl.id}
                className="p-4 rounded-2xl bg-spotify-surface/40 border border-spotify-border flex items-center justify-between"
              >
                <div>
                  <h4 className="font-bold text-sm text-white">{pl.name}</h4>
                  <p className="text-xs text-spotify-muted mt-0.5">
                    {pl.track_count} faixas • Clima: {pl.mood_label || pl.mood}
                  </p>
                </div>
                {pl.spotify_url && (
                  <a
                    href={pl.spotify_url}
                    target="_blank"
                    rel="noreferrer"
                    className="p-2 rounded-xl bg-spotify-surface hover:text-spotify-green text-spotify-muted transition-colors"
                  >
                    <ExternalLink className="w-4 h-4" />
                  </a>
                )}
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};
