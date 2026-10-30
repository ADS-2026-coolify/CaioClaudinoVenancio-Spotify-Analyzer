import React from 'react';
import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { AuthProvider, useAuth } from '@/contexts/AuthContext';
import { AppLayout } from '@/components/layout/AppLayout';
import { LandingPage } from '@/pages/LandingPage';
import { DashboardPage } from '@/pages/DashboardPage';
import { ArtistsPage } from '@/pages/ArtistsPage';
import { TracksPage } from '@/pages/TracksPage';
import { GenresPage } from '@/pages/GenresPage';
import { DecadesPage } from '@/pages/DecadesPage';
import { RecentlyPlayedPage } from '@/pages/RecentlyPlayedPage';
import { InsightsPage } from '@/pages/InsightsPage';
import { PlaylistsPage } from '@/pages/PlaylistsPage';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5, // 5 minutes
      refetchOnWindowFocus: false,
      retry: 1,
    },
  },
});

// Guard that redirects unauthenticated users to landing page
const ProtectedRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { isAuthenticated, isDemo, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-h-screen bg-spotify-black flex items-center justify-center">
        <div className="w-10 h-10 border-4 border-spotify-green border-t-transparent rounded-full animate-spin" />
      </div>
    );
  }

  if (!isAuthenticated && !isDemo) {
    return <Navigate to="/" replace />;
  }

  return <>{children}</>;
};

export const AppRoutes: React.FC = () => {
  return (
    <Routes>
      {/* Public Landing Page */}
      <Route path="/" element={<LandingPage />} />
      <Route path="/login" element={<LandingPage />} />

      {/* Protected App Routes */}
      <Route
        element={
          <ProtectedRoute>
            <AppLayout />
          </ProtectedRoute>
        }
      >
        <Route path="/dashboard" element={<DashboardPage />} />
        <Route path="/analytics/artists" element={<ArtistsPage />} />
        <Route path="/analytics/tracks" element={<TracksPage />} />
        <Route path="/analytics/genres" element={<GenresPage />} />
        <Route path="/analytics/decades" element={<DecadesPage />} />
        <Route path="/analytics/recently-played" element={<RecentlyPlayedPage />} />
        <Route path="/analytics/insights" element={<InsightsPage />} />
        <Route path="/playlists" element={<PlaylistsPage />} />
      </Route>

      {/* Catch-all */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
};

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <BrowserRouter>
          <AppRoutes />
        </BrowserRouter>
      </AuthProvider>
    </QueryClientProvider>
  );
}
