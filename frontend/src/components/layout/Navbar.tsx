import React from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { LogOut, Music2, Sparkles, User as UserIcon } from 'lucide-react';

interface NavbarProps {
  onToggleSidebar?: () => void;
}

export const Navbar: React.FC<NavbarProps> = ({ onToggleSidebar }) => {
  const { user, isDemo, loginWithSpotify, logout } = useAuth();

  return (
    <header className="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-spotify-border bg-spotify-black/80 px-4 md:px-8 backdrop-blur-md">
      {/* Mobile brand & toggle */}
      <div className="flex items-center gap-3">
        {onToggleSidebar && (
          <button
            onClick={onToggleSidebar}
            className="md:hidden p-2 rounded-lg bg-spotify-surface text-spotify-muted hover:text-white"
            aria-label="Toggle Menu"
          >
            <svg
              className="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
        )}

        <div className="flex items-center gap-2.5">
          <div className="w-8 h-8 rounded-full bg-spotify-green flex items-center justify-center text-black shadow-md shadow-spotify-green/20">
            <Music2 className="w-4 h-4 fill-current stroke-none" />
          </div>
          <span className="font-bold text-base tracking-tight text-white hidden sm:inline-block">
            Spotify <span className="text-spotify-green">Profile Analyzer</span>
          </span>
        </div>
      </div>

      {/* Right action area */}
      <div className="flex items-center gap-3 sm:gap-4">
        {isDemo && (
          <div className="flex items-center gap-2">
            <span className="hidden lg:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
              <Sparkles className="w-3.5 h-3.5" />
              Modo Demonstração
            </span>
            <button
              onClick={loginWithSpotify}
              className="px-3.5 py-1.5 rounded-full text-xs font-bold bg-spotify-green hover:bg-spotify-green-light text-black transition-transform active:scale-95 shadow-md shadow-spotify-green/20"
            >
              Conectar Spotify Real
            </button>
          </div>
        )}

        {user && (
          <div className="flex items-center gap-3 pl-3 border-l border-spotify-border">
            <div className="flex items-center gap-2.5">
              {user.avatar_url ? (
                <img
                  src={user.avatar_url}
                  alt={user.display_name}
                  className="w-8 h-8 rounded-full object-cover ring-1 ring-spotify-border"
                />
              ) : (
                <div className="w-8 h-8 rounded-full bg-spotify-surface flex items-center justify-center text-spotify-muted">
                  <UserIcon className="w-4 h-4" />
                </div>
              )}
              <div className="hidden md:flex flex-col text-left">
                <span className="text-xs font-semibold text-white max-w-[120px] truncate">
                  {user.display_name || user.name}
                </span>
                <span className="text-[10px] text-spotify-muted uppercase tracking-wider">
                  {user.product || 'Spotify'}
                </span>
              </div>
            </div>

            <button
              onClick={logout}
              title={isDemo ? 'Sair do Modo Demonstração' : 'Desconectar conta'}
              className="p-2 rounded-lg text-spotify-muted hover:text-red-400 hover:bg-red-500/10 transition-colors"
            >
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        )}
      </div>
    </header>
  );
};
