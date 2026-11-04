import React, { useState } from 'react';
import { Outlet } from 'react-router-dom';
import { Navbar } from './Navbar';
import { Sidebar } from './Sidebar';
import { useAuth } from '@/contexts/AuthContext';
import { Info, Sparkles } from 'lucide-react';

export const AppLayout: React.FC = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const { isDemo, loginWithSpotify } = useAuth();

  return (
    <div className="min-h-screen bg-spotify-black text-white flex flex-col">
      <Navbar onToggleSidebar={() => setSidebarOpen((prev) => !prev)} />

      {/* Demo Warning Banner */}
      {isDemo && (
        <div className="bg-gradient-to-r from-amber-500/20 via-spotify-dark to-amber-500/20 border-b border-amber-500/30 px-4 py-2.5 text-xs text-amber-300 flex items-center justify-between">
          <div className="flex items-center gap-2 mx-auto">
            <Sparkles className="w-4 h-4 shrink-0 text-amber-400" />
            <span>
              <strong>Você está navegando no Modo Demonstração</strong> com dados fictícios.
            </span>
            <button
              onClick={loginWithSpotify}
              className="ml-2 font-semibold underline hover:text-white transition-colors"
            >
              Conecte seu Spotify real para ver seus dados →
            </button>
          </div>
        </div>
      )}

      <div className="flex flex-1">
        <Sidebar isOpen={sidebarOpen} onClose={() => setSidebarOpen(false)} />
        <main className="flex-1 overflow-y-auto px-4 py-6 md:px-8 lg:py-8 max-w-7xl mx-auto w-full">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

