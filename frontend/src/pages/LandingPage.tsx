import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import {
  Calendar,
  Compass,
  Headphones,
  ListMusic,
  Lock,
  Mic2,
  Music2,
  Play,
  ShieldCheck,
  Sparkles,
  Zap,
} from 'lucide-react';
import { motion } from 'framer-motion';

export const LandingPage: React.FC = () => {
  const { loginWithSpotify, enableDemoMode, isAuthenticated } = useAuth();
  const navigate = useNavigate();

  const handleStart = () => {
    if (isAuthenticated) {
      navigate('/dashboard');
    } else {
      loginWithSpotify();
    }
  };

  const handleDemo = () => {
    enableDemoMode();
    navigate('/dashboard');
  };

  return (
    <div className="min-h-screen bg-spotify-black text-white flex flex-col selection:bg-spotify-green selection:text-black">
      {/* Navbar */}
      <header className="sticky top-0 z-50 backdrop-blur-md bg-spotify-black/80 border-b border-spotify-border/60 px-6 py-4">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-9 h-9 rounded-full bg-spotify-green flex items-center justify-center text-black shadow-lg shadow-spotify-green/20">
              <Music2 className="w-5 h-5 fill-current stroke-none" />
            </div>
            <span className="font-extrabold text-lg tracking-tight text-white">
              Spotify <span className="text-spotify-green">Profile Analyzer</span>
            </span>
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={handleDemo}
              className="px-4 py-2 text-xs md:text-sm font-semibold rounded-full border border-spotify-border hover:border-spotify-green/60 text-spotify-muted hover:text-white transition-all"
            >
              Ver Modo Demo
            </button>
            <button
              onClick={handleStart}
              className="px-5 py-2 text-xs md:text-sm font-bold rounded-full bg-spotify-green hover:bg-spotify-green-light text-black transition-all shadow-md shadow-spotify-green/20 active:scale-95"
            >
              {isAuthenticated ? 'Abrir Dashboard' : 'Conectar Spotify'}
            </button>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative overflow-hidden pt-16 pb-20 md:pt-24 md:pb-28">
        {/* Glow ambient background circles */}
        <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-spotify-green/15 blur-[140px] pointer-events-none rounded-full" />
        <div className="absolute top-1/3 right-10 w-[350px] h-[350px] bg-emerald-500/10 blur-[100px] pointer-events-none rounded-full" />

        <div className="max-w-5xl mx-auto px-6 text-center relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
            className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-spotify-surface border border-spotify-border text-xs font-semibold text-spotify-green mb-6"
          >
            <Sparkles className="w-3.5 h-3.5" />
            <span>Sua retrospectiva musical viva em tempo real</span>
          </motion.div>

          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.1 }}
            className="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight"
          >
            Descubra o que sua música{' '}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-spotify-green via-emerald-400 to-green-300">
              diz sobre você.
            </span>
          </motion.h1>

          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="text-base sm:text-xl text-spotify-muted max-w-2xl mx-auto mb-10 leading-relaxed"
          >
            Conecte sua conta Spotify com segurança e receba diagnósticos visuais e
            divertidos dos seus artistas favoritos, sua década musical predominante, seu gênero mais obscuro e crie playlists sob medida.
          </motion.p>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.3 }}
            className="flex flex-col sm:flex-row items-center justify-center gap-4"
          >
            <button
              onClick={handleStart}
              className="w-full sm:w-auto px-8 py-4 rounded-full bg-spotify-green hover:bg-spotify-green-light text-black font-extrabold text-base md:text-lg flex items-center justify-center gap-3 transition-transform hover:scale-105 active:scale-95 shadow-xl shadow-spotify-green/25"
            >
              <Music2 className="w-5 h-5 fill-current stroke-none" />
              Analisar meu Spotify
            </button>
            <button
              onClick={handleDemo}
              className="w-full sm:w-auto px-8 py-4 rounded-full bg-spotify-surface hover:bg-spotify-surface-hover border border-spotify-border text-white font-semibold text-base transition-colors"
            >
              Conhecer Modo Demonstração
            </button>
          </motion.div>

          {/* Privacy badge */}
          <div className="mt-8 flex items-center justify-center gap-2 text-xs text-spotify-subtext">
            <ShieldCheck className="w-4 h-4 text-spotify-green" />
            <span>OAuth 2.0 seguro. Nunca armazenamos senhas ou postamos sem sua permissão.</span>
          </div>

          {/* Interactive Preview Mockup */}
          <motion.div
            initial={{ opacity: 0, y: 40 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7, delay: 0.4 }}
            className="mt-16 rounded-3xl border border-spotify-border bg-gradient-to-b from-spotify-dark/90 to-spotify-black p-4 sm:p-6 md:p-8 shadow-2xl glow-card text-left max-w-4xl mx-auto"
          >
            <div className="flex items-center justify-between pb-6 border-b border-spotify-border/60">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-full ring-2 ring-spotify-green/40 overflow-hidden bg-spotify-surface">
                  <img
                    src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&h=150&fit=crop"
                    alt="Preview"
                    className="w-full h-full object-cover"
                  />
                </div>
                <div>
                  <div className="text-xs uppercase font-bold text-spotify-green tracking-wider">
                    Boa tarde, Maria
                  </div>
                  <h3 className="text-lg md:text-xl font-bold text-white">
                    Seu Perfil Musical ao Vivo
                  </h3>
                </div>
              </div>
              <div className="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-spotify-surface border border-spotify-border text-xs text-spotify-muted">
                <Headphones className="w-3.5 h-3.5 text-spotify-green" />
                <span>Últimos 6 meses</span>
              </div>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 my-6">
              <div className="p-4 rounded-xl bg-spotify-surface/60 border border-spotify-border/40">
                <span className="text-[11px] uppercase font-bold text-spotify-subtext">Gênero Forte</span>
                <div className="text-xl font-bold text-white mt-1">Indie Rock</div>
                <span className="text-xs text-spotify-green">26.5% das preferências</span>
              </div>
              <div className="p-4 rounded-xl bg-spotify-surface/60 border border-spotify-border/40">
                <span className="text-[11px] uppercase font-bold text-spotify-subtext">Sua Década</span>
                <div className="text-xl font-bold text-white mt-1">Anos 2010</div>
                <span className="text-xs text-spotify-muted">18 faixas no Top 50</span>
              </div>
              <div className="p-4 rounded-xl bg-spotify-surface/60 border border-spotify-border/40">
                <span className="text-[11px] uppercase font-bold text-spotify-subtext">Artista Favorito</span>
                <div className="text-xl font-bold text-white mt-1 truncate">Arctic Monkeys</div>
                <span className="text-xs text-amber-400">#1 mais escutado</span>
              </div>
              <div className="p-4 rounded-xl bg-spotify-surface/60 border border-spotify-border/40">
                <span className="text-[11px] uppercase font-bold text-spotify-subtext">Mais Obscuro</span>
                <div className="text-xl font-bold text-white mt-1 truncate">Tropicália Psico</div>
                <span className="text-xs text-purple-400">Raridade no perfil</span>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 border-t border-spotify-border/60 bg-spotify-dark/40">
        <div className="max-w-7xl mx-auto px-6">
          <div className="text-center max-w-2xl mx-auto mb-16">
            <span className="text-xs font-bold uppercase tracking-wider text-spotify-green">
              Funcionalidades
            </span>
            <h2 className="text-3xl sm:text-4xl font-extrabold text-white mt-2">
              Mergulhe fundo nos seus hábitos sonoros
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="p-8 rounded-3xl border border-spotify-border bg-spotify-dark/70 hover:border-spotify-border-light transition-all">
              <div className="w-12 h-12 rounded-2xl bg-spotify-green/10 text-spotify-green flex items-center justify-center mb-6">
                <Mic2 className="w-6 h-6" />
              </div>
              <h3 className="text-xl font-bold text-white mb-2">Rankings com 3 Intervalos</h3>
              <p className="text-sm text-spotify-muted leading-relaxed">
                Compare seus artistas e músicas favoritos das últimas 4 semanas, últimos 6 meses e de todos os tempos através dos dados oficiais da API.
              </p>
            </div>

            <div className="p-8 rounded-3xl border border-spotify-border bg-spotify-dark/70 hover:border-spotify-border-light transition-all">
              <div className="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center mb-6">
                <Compass className="w-6 h-6" />
              </div>
              <h3 className="text-xl font-bold text-white mb-2">Gêneros e O Gênero Mais Obscuro</h3>
              <p className="text-sm text-spotify-muted leading-relaxed">
                Visualização detalhada da sua distribuição por gêneros musicais e um destaque especial com o gênero mais raro encontrado nas suas reproduções.
              </p>
            </div>

            <div className="p-8 rounded-3xl border border-spotify-border bg-spotify-dark/70 hover:border-spotify-border-light transition-all">
              <div className="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center mb-6">
                <Calendar className="w-6 h-6" />
              </div>
              <h3 className="text-xl font-bold text-white mb-2">Sua Década Musical</h3>
              <p className="text-sm text-spotify-muted leading-relaxed">
                Mapeamento cronológico das décadas que moldaram sua biblioteca — dos clássicos dos anos 1970 às novidades quentes dos anos 2020.
              </p>
            </div>

            <div className="p-8 rounded-3xl border border-spotify-border bg-spotify-dark/70 hover:border-spotify-border-light transition-all">
              <div className="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-6">
                <ListMusic className="w-6 h-6" />
              </div>
              <h3 className="text-xl font-bold text-white mb-2">Gerador de Playlists por Clima</h3>
              <p className="text-sm text-spotify-muted leading-relaxed">
                Escolha momentos como Chuva, Treino, Foco ou Madrugada e gere playlists inteligentes salvas diretamente na sua conta Spotify.
              </p>
            </div>

            <div className="p-8 rounded-3xl border border-spotify-border bg-spotify-dark/70 hover:border-spotify-border-light transition-all">
              <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-6">
                <Sparkles className="w-6 h-6" />
              </div>
              <h3 className="text-xl font-bold text-white mb-2">Insights Determinísticos</h3>
              <p className="text-sm text-spotify-muted leading-relaxed">
                Frases e diagnósticos gerados estritamente com base nos números e na diversidade do seu catálogo sonoro, sem invenções.
              </p>
            </div>

            <div className="p-8 rounded-3xl border border-spotify-border bg-spotify-dark/70 hover:border-spotify-border-light transition-all">
              <div className="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center mb-6">
                <Lock className="w-6 h-6" />
              </div>
              <h3 className="text-xl font-bold text-white mb-2">Segurança em Primeiro Lugar</h3>
              <p className="text-sm text-spotify-muted leading-relaxed">
                Tokens criptografados no banco de dados, escopos mínimos solicitados e opção de desconectar sua conta com um clique a qualquer hora.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* How it works */}
      <section className="py-20 border-t border-spotify-border/60">
        <div className="max-w-5xl mx-auto px-6">
          <div className="text-center mb-16">
            <span className="text-xs font-bold uppercase tracking-wider text-spotify-green">
              Simples & Rápido
            </span>
            <h2 className="text-3xl sm:text-4xl font-extrabold text-white mt-2">
              Como funciona o Spotify Profile Analyzer
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div className="flex flex-col items-center">
              <div className="w-14 h-14 rounded-2xl bg-spotify-surface border border-spotify-border flex items-center justify-center text-xl font-black text-spotify-green mb-4 shadow-md">
                1
              </div>
              <h4 className="text-lg font-bold text-white mb-2">Conecte sua Conta</h4>
              <p className="text-sm text-spotify-muted">
                Autorize com segurança através do login oficial do Spotify Web API.
              </p>
            </div>

            <div className="flex flex-col items-center">
              <div className="w-14 h-14 rounded-2xl bg-spotify-surface border border-spotify-border flex items-center justify-center text-xl font-black text-spotify-green mb-4 shadow-md">
                2
              </div>
              <h4 className="text-lg font-bold text-white mb-2">Processamento Real</h4>
              <p className="text-sm text-spotify-muted">
                Nosso motor analisa artistas, faixas, datas de lançamento e frequências de escuta.
              </p>
            </div>

            <div className="flex flex-col items-center">
              <div className="w-14 h-14 rounded-2xl bg-spotify-surface border border-spotify-border flex items-center justify-center text-xl font-black text-spotify-green mb-4 shadow-md">
                3
              </div>
              <h4 className="text-lg font-bold text-white mb-2">Descubra & Crie</h4>
              <p className="text-sm text-spotify-muted">
                Explore seus insights, gráficos interativos e crie playlists para cada momento.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="mt-auto border-t border-spotify-border/60 py-10 px-6 bg-spotify-black text-xs text-spotify-subtext">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
          <div className="flex items-center gap-2">
            <Music2 className="w-4 h-4 text-spotify-green" />
            <span className="font-semibold text-white">Spotify Profile Analyzer</span>
            <span>— Não afiliado oficialmente ao Spotify AB. Desenvolvido para portfólio.</span>
          </div>
          <div>
            <span>Desenvolvido com Laravel 12+ & React + TypeScript + Tailwind CSS</span>
          </div>
        </div>
      </footer>
    </div>
  );
};
