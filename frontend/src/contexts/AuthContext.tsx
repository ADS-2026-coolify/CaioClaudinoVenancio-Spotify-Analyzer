import React, { createContext, useContext, useEffect, useState } from 'react';
import { api } from '@/api/endpoints';
import type { User } from '@/types';

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  isDemo: boolean;
  isLoading: boolean;
  loginWithSpotify: () => Promise<void>;
  enableDemoMode: () => void;
  exitDemoMode: () => void;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);
  const [isDemo, setIsDemo] = useState<boolean>(() => {
    return localStorage.getItem('spotify_demo_mode') === 'true';
  });
  const [isLoading, setIsLoading] = useState<boolean>(true);

  const checkUserStatus = async () => {
    try {
      setIsLoading(true);

      // Check if arriving from Spotify OAuth callback with auth_token
      const urlParams = new URLSearchParams(window.location.search);
      const tokenFromUrl = urlParams.get('auth_token');
      if (tokenFromUrl) {
        localStorage.setItem('auth_token', tokenFromUrl);
        localStorage.removeItem('spotify_demo_mode');
        urlParams.delete('auth_token');
        urlParams.delete('auth');
        const remainingQuery = urlParams.toString();
        const cleanUrl = window.location.pathname + (remainingQuery ? `?${remainingQuery}` : '');
        window.history.replaceState({}, '', cleanUrl);
      }

      const isDemoActive = localStorage.getItem('spotify_demo_mode') === 'true';

      const data = await api.getCurrentUser();
      if (data.authenticated && data.user) {
        setUser(data.user);
        setIsAuthenticated(true);
        setIsDemo(false);
        localStorage.removeItem('spotify_demo_mode');
      } else if (isDemoActive) {
        // Keep demo active
        setUser({
          id: 9999,
          spotify_id: 'demo_user',
          name: 'Maria Clara',
          display_name: 'Maria Clara (Modo Demonstração)',
          country: 'BR',
          product: 'premium',
          avatar_url:
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=300&fit=crop&crop=faces',
        });
        setIsAuthenticated(true);
      } else {
        setUser(null);
        setIsAuthenticated(false);
      }
    } catch {
      const isDemoActive = localStorage.getItem('spotify_demo_mode') === 'true';
      if (isDemoActive) {
        setUser({
          id: 9999,
          spotify_id: 'demo_user',
          name: 'Maria Clara',
          display_name: 'Maria Clara (Modo Demonstração)',
          country: 'BR',
          product: 'premium',
          avatar_url:
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=300&fit=crop&crop=faces',
        });
        setIsAuthenticated(true);
      } else {
        setUser(null);
        setIsAuthenticated(false);
      }
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    checkUserStatus();
  }, [isDemo]);

  const loginWithSpotify = async () => {
    try {
      localStorage.removeItem('spotify_demo_mode');
      localStorage.removeItem('auth_token');
      const data = await api.getAuthUrl();
      if (data.auth_url) {
        window.location.href = data.auth_url;
      }
    } catch (err) {
      console.error('Falha ao iniciar autenticação Spotify:', err);
      alert('Não foi possível conectar ao servidor Spotify. Verifique se o backend está em execução.');
    }
  };

  const enableDemoMode = () => {
    localStorage.removeItem('auth_token');
    localStorage.setItem('spotify_demo_mode', 'true');
    setIsDemo(true);
    setUser({
      id: 9999,
      spotify_id: 'demo_user',
      name: 'Maria Clara',
      display_name: 'Maria Clara (Modo Demonstração)',
      country: 'BR',
      product: 'premium',
      avatar_url:
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=300&fit=crop&crop=faces',
    });
    setIsAuthenticated(true);
  };

  const exitDemoMode = () => {
    localStorage.removeItem('spotify_demo_mode');
    localStorage.removeItem('auth_token');
    setIsDemo(false);
    setUser(null);
    setIsAuthenticated(false);
  };

  const logout = async () => {
    if (isDemo) {
      exitDemoMode();
      return;
    }

    try {
      await api.disconnect();
    } catch (e) {
      console.warn('Erro ao desconectar sessão:', e);
    } finally {
      localStorage.removeItem('spotify_demo_mode');
      localStorage.removeItem('auth_token');
      setUser(null);
      setIsAuthenticated(false);
      setIsDemo(false);
      window.location.href = '/';
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        isAuthenticated,
        isDemo,
        isLoading,
        loginWithSpotify,
        enableDemoMode,
        exitDemoMode,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth deve ser utilizado dentro de um AuthProvider');
  }
  return context;
};

