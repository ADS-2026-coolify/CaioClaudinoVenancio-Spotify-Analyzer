import React from 'react';
import { NavLink } from 'react-router-dom';
import {
  Calendar,
  Compass,
  History,
  LayoutDashboard,
  ListMusic,
  Mic2,
  Music,
  Sparkles,
} from 'lucide-react';

interface SidebarProps {
  isOpen?: boolean;
  onClose?: () => void;
}

const navItems = [
  { to: '/dashboard', label: 'Visão Geral', icon: LayoutDashboard },
  { to: '/analytics/artists', label: 'Top Artistas', icon: Mic2 },
  { to: '/analytics/tracks', label: 'Top Músicas', icon: Music },
  { to: '/analytics/genres', label: 'Gêneros', icon: Compass },
  { to: '/analytics/decades', label: 'Décadas', icon: Calendar },
  { to: '/analytics/recently-played', label: 'Histórico Recente', icon: History },
  { to: '/analytics/insights', label: 'Seu Perfil Musical', icon: Sparkles },
  { to: '/playlists', label: 'Gerador de Playlists', icon: ListMusic, badge: 'Especial' },
];

export const Sidebar: React.FC<SidebarProps> = ({ isOpen, onClose }) => {
  return (
    <>
      {/* Mobile backdrop */}
      {isOpen && (
        <div
          onClick={onClose}
          className="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm md:hidden"
        />
      )}

      <aside
        className={`fixed top-16 bottom-0 left-0 z-40 w-64 border-r border-spotify-border bg-spotify-black/95 p-4 transition-transform duration-300 md:static md:translate-x-0 ${
          isOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="space-y-6">
          <div>
            <div className="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-spotify-subtext">
              Análise Pessoal
            </div>
            <nav className="space-y-1">
              {navItems.slice(0, 6).map((item) => {
                const Icon = item.icon;
                return (
                  <NavLink
                    key={item.to}
                    to={item.to}
                    onClick={onClose}
                    className={({ isActive }) =>
                      `flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all ${
                        isActive
                          ? 'bg-spotify-green text-black font-semibold shadow-sm shadow-spotify-green/20'
                          : 'text-spotify-muted hover:text-white hover:bg-spotify-surface'
                      }`
                    }
                  >
                    <Icon className="w-4 h-4 shrink-0" />
                    <span className="truncate">{item.label}</span>
                  </NavLink>
                );
              })}
            </nav>
          </div>

          <div>
            <div className="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-spotify-subtext">
              Experiências
            </div>
            <nav className="space-y-1">
              {navItems.slice(6).map((item) => {
                const Icon = item.icon;
                return (
                  <NavLink
                    key={item.to}
                    to={item.to}
                    onClick={onClose}
                    className={({ isActive }) =>
                      `flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all ${
                        isActive
                          ? 'bg-spotify-green text-black font-semibold shadow-sm shadow-spotify-green/20'
                          : 'text-spotify-muted hover:text-white hover:bg-spotify-surface'
                      }`
                    }
                  >
                    <div className="flex items-center gap-3 truncate">
                      <Icon className="w-4 h-4 shrink-0" />
                      <span className="truncate">{item.label}</span>
                    </div>
                    {item.badge && (
                      <span className="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-spotify-green/20 text-spotify-green">
                        {item.badge}
                      </span>
                    )}
                  </NavLink>
                );
              })}
            </nav>
          </div>
        </div>

        {/* Footer tip */}
        <div className="absolute bottom-4 left-4 right-4 p-3 rounded-xl border border-spotify-border/60 bg-spotify-dark/50">
          <p className="text-[11px] text-spotify-subtext leading-relaxed">
            Dados sincronizados diretamente da sua conta oficial Spotify via OAuth 2.0.
          </p>
        </div>
      </aside>
    </>
  );
};
