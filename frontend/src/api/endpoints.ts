import { apiClient } from './client';
import type {
  Artist,
  CreatedPlaylist,
  DashboardData,
  DecadeAnalysis,
  GeneratedPlaylistPreview,
  GenreAnalysis,
  Insight,
  MoodCategory,
  RecentlyPlayedItem,
  TimeRange,
  Track,
  User,
} from '@/types';

export const api = {
  // Auth
  getAuthUrl: async () => {
    const res = await apiClient.get<{ auth_url: string; state: string }>('/spotify/auth');
    return res.data;
  },

  getCurrentUser: async () => {
    const res = await apiClient.get<{
      authenticated: boolean;
      has_spotify: boolean;
      user: User | null;
    }>('/user');
    return res.data;
  },

  disconnect: async () => {
    const res = await apiClient.delete<{ message: string; disconnected: boolean }>('/spotify/disconnect');
    return res.data;
  },

  // Dashboard
  getDashboard: async (isDemo = false): Promise<DashboardData> => {
    const endpoint = isDemo ? '/demo/dashboard' : '/dashboard';
    const res = await apiClient.get<{ success: boolean; data: DashboardData }>(endpoint);
    return res.data.data;
  },

  // Artists
  getTopArtists: async (timeRange: TimeRange = 'medium_term', isDemo = false): Promise<Artist[]> => {
    const endpoint = isDemo ? '/demo/analytics/artists' : '/analytics/artists';
    const res = await apiClient.get<{ success: boolean; artists: Artist[] }>(endpoint, {
      params: { time_range: timeRange },
    });
    return res.data.artists;
  },

  // Tracks
  getTopTracks: async (timeRange: TimeRange = 'medium_term', isDemo = false): Promise<Track[]> => {
    const endpoint = isDemo ? '/demo/analytics/tracks' : '/analytics/tracks';
    const res = await apiClient.get<{ success: boolean; tracks: Track[] }>(endpoint, {
      params: { time_range: timeRange },
    });
    return res.data.tracks;
  },

  // Genres
  getGenres: async (timeRange: TimeRange = 'medium_term', isDemo = false): Promise<GenreAnalysis> => {
    const endpoint = isDemo ? '/demo/analytics/genres' : '/analytics/genres';
    const res = await apiClient.get<{ success: boolean; data: GenreAnalysis }>(endpoint, {
      params: { time_range: timeRange },
    });
    return res.data.data;
  },

  // Decades
  getDecades: async (timeRange: TimeRange = 'medium_term', isDemo = false): Promise<DecadeAnalysis> => {
    const endpoint = isDemo ? '/demo/analytics/decades' : '/analytics/decades';
    const res = await apiClient.get<{ success: boolean; data: DecadeAnalysis }>(endpoint, {
      params: { time_range: timeRange },
    });
    return res.data.data;
  },

  // Recently Played
  getRecentlyPlayed: async (isDemo = false): Promise<{ notice?: string; tracks: RecentlyPlayedItem[] }> => {
    const endpoint = isDemo ? '/demo/analytics/recently-played' : '/analytics/recently-played';
    const res = await apiClient.get<{ success: boolean; notice?: string; tracks: RecentlyPlayedItem[] }>(endpoint);
    return { notice: res.data.notice, tracks: res.data.tracks };
  },

  // Insights
  getInsights: async (isDemo = false): Promise<Insight[]> => {
    const endpoint = isDemo ? '/demo/analytics/insights' : '/analytics/insights';
    const res = await apiClient.get<{ success: boolean; insights: Insight[] }>(endpoint);
    return res.data.insights;
  },

  // Playlists
  generatePlaylistPreview: async (
    mood: MoodCategory,
    trackCount: number = 20,
    isDemo = false
  ): Promise<GeneratedPlaylistPreview> => {
    const endpoint = isDemo ? '/demo/playlists/generate' : '/playlists/generate';
    const res = await apiClient.post<{ success: boolean; data: GeneratedPlaylistPreview }>(endpoint, {
      mood,
      track_count: trackCount,
    });
    return res.data.data;
  },

  createSpotifyPlaylist: async (params: {
    name: string;
    description?: string;
    is_public?: boolean;
    mood: MoodCategory;
    track_uris: string[];
  }): Promise<CreatedPlaylist> => {
    const res = await apiClient.post<{ success: boolean; playlist: CreatedPlaylist }>('/playlists/create', params);
    return res.data.playlist;
  },

  getUserPlaylists: async (): Promise<CreatedPlaylist[]> => {
    const res = await apiClient.get<{ success: boolean; playlists: CreatedPlaylist[] }>('/playlists');
    return res.data.playlists;
  },
};
