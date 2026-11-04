export interface User {
  id: number;
  spotify_id: string;
  name: string;
  display_name: string;
  email?: string;
  avatar_url?: string;
  country?: string;
  product?: string;
  last_login_at?: string;
}

export type TimeRange = 'short_term' | 'medium_term' | 'long_term';

export interface Artist {
  rank: number;
  id: string;
  name: string;
  genres: string[];
  popularity: number;
  followers?: number;
  image_url?: string;
  thumbnail_url?: string;
  spotify_url?: string;
}

export interface Track {
  rank: number;
  id: string;
  uri?: string;
  name: string;
  artists: string;
  album: string;
  release_date?: string;
  decade?: string;
  popularity: number;
  duration_ms?: number;
  duration_formatted?: string;
  image_url?: string;
  thumbnail_url?: string;
  spotify_url?: string;
}

export interface RecentlyPlayedItem {
  id: string;
  name: string;
  artists: string;
  album: string;
  played_at: string;
  played_at_human?: string;
  duration_formatted?: string;
  image_url?: string;
  thumbnail_url?: string;
  spotify_url?: string;
}

export interface GenreItem {
  name: string;
  count: number;
  percentage: number;
}

export interface ObscureGenre {
  name: string;
  count: number;
  percentage: number;
  artist_sample: string;
}

export interface GenreAnalysis {
  genres: GenreItem[];
  top_genres: GenreItem[];
  rare_genres: GenreItem[];
  obscure_genre: ObscureGenre | null;
  total_unique_genres: number;
  total_genre_occurrences: number;
  diversity_score: number;
}

export interface RepresentativeTrack {
  id: string;
  name: string;
  artist: string;
  album: string;
  release_date: string;
  image_url?: string;
  spotify_url?: string;
}

export interface DecadeItem {
  decade: string;
  label: string;
  count: number;
  percentage: number;
  representative_track?: RepresentativeTrack | null;
}

export interface DecadeAnalysis {
  decades: DecadeItem[];
  predominant_decade: string | null;
  total_tracks_analyzed: number;
}

export interface Insight {
  category: string;
  tag: string;
  title: string;
  description: string;
  icon: string;
}

export interface DashboardData {
  user: User;
  greeting: string;
  stats: {
    total_artists_analyzed: number;
    total_tracks_analyzed: number;
    predominant_genre: string;
    predominant_decade: string;
    favorite_artist: {
      id: string;
      name: string;
      image_url?: string;
      popularity: number;
      genres: string[];
    } | null;
    top_track: {
      id: string;
      name: string;
      artist: string;
      album: string;
      image_url?: string;
      spotify_url?: string;
    } | null;
    unique_genres_count: number;
    diversity_score: number;
  };
  top_artists_preview: Artist[];
  top_tracks_preview: Track[];
  top_genres_preview: GenreItem[];
  decades_summary: DecadeItem[];
  recently_played_preview: RecentlyPlayedItem[];
  insights_preview: Insight[];
}

export type MoodCategory =
  | 'rain'
  | 'party'
  | 'focus'
  | 'relax'
  | 'nostalgia'
  | 'workout'
  | 'road_trip'
  | 'late_night'
  | 'romantic';

export interface MoodOption {
  value: MoodCategory;
  label: string;
  emoji: string;
  description: string;
}

export interface GeneratedPlaylistPreview {
  mood: string;
  mood_label: string;
  mood_emoji: string;
  tracks: Array<{
    id: string;
    uri: string;
    name: string;
    artists: string;
    album: string;
    release_date: string;
    duration_ms: number;
    duration_formatted: string;
    image_url?: string;
    spotify_url?: string;
    is_favorite_artist?: boolean;
  }>;
  total_tracks: number;
  estimated_duration_minutes: number;
}

export interface CreatedPlaylist {
  id: number;
  spotify_playlist_id?: string;
  name: string;
  description?: string;
  mood: string;
  mood_label?: string;
  track_count: number;
  is_public: boolean;
  spotify_url?: string;
  created_at: string;
}

