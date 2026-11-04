import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { api } from '@/api/endpoints';
import { useAuth } from '@/contexts/AuthContext';
import { CardSkeleton } from '@/components/ui/LoadingSkeleton';
import { ErrorMessage } from '@/components/ui/ErrorMessage';
import {
  Activity,
  Compass,
  Crown,
  Disc,
  Flame,
  Radio,
  Repeat,
  ShieldCheck,
  Shuffle,
  Sparkles,
  Target,
} from 'lucide-react';
import { motion } from 'framer-motion';

const iconMap: Record<string, React.ReactNode> = {
  Flame: <Flame className="w-5 h-5 text-amber-400" />,
  Compass: <Compass className="w-5 h-5 text-spotify-green" />,
  Target: <Target className="w-5 h-5 text-blue-400" />,
  Sparkles: <Sparkles className="w-5 h-5 text-purple-400" />,
  Radio: <Radio className="w-5 h-5 text-emerald-400" />,
  Disc: <Disc className="w-5 h-5 text-indigo-400" />,
  Crown: <Crown className="w-5 h-5 text-yellow-400" />,
  Shuffle: <Shuffle className="w-5 h-5 text-teal-400" />,
  Repeat: <Repeat className="w-5 h-5 text-rose-400" />,
  Activity: <Activity className="w-5 h-5 text-spotify-green" />,
};

export const InsightsPage: React.FC = () => {
  const { isDemo } = useAuth();

  const {
    data: insights = [],
    isLoading,
    isError,
    refetch,
  } = useQuery({
    queryKey: ['insights', isDemo],
    queryFn: () => api.getInsights(isDemo),
  });

  return (
    <div className="space-y-8 pb-12">
      {/* Header */}
      <div>
        <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-spotify-green">
          <Sparkles className="w-4 h-4" />
          Seu Perfil Musical
        </div>
        <h1 className="text-3xl font-extrabold text-white mt-1">Insights Determinísticos</h1>
        <p className="text-sm text-spotify-muted mt-1">
          Padrões e conclusões extraídos puramente dos números e da variedade do seu catálogo.
        </p>
      </div>

      {/* Deterministic Explanation Alert */}
      <div className="p-4 rounded-2xl border border-spotify-border/80 bg-spotify-dark/80 flex items-start gap-3">
        <ShieldCheck className="w-5 h-5 text-spotify-green shrink-0 mt-0.5" />
        <div className="text-xs text-spotify-muted leading-relaxed">
          <strong className="text-white">Metodologia baseada em dados:</strong> Todos os insights
          abaixo são calculados através de regras lógicas estritas sobre a frequência de gêneros,
          décadas e variedade de artistas do seu perfil. Não simulamos ou inventamos
          características psicológicas.
        </div>
      </div>

      {isLoading ? (
        <CardSkeleton count={6} />
      ) : isError ? (
        <ErrorMessage onRetry={() => refetch()} />
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {insights.map((insight, index) => (
            <motion.div
              key={index}
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.3, delay: index * 0.05 }}
              className="rounded-3xl border border-spotify-border bg-spotify-dark/80 p-6 flex flex-col justify-between hover:border-spotify-green/40 transition-all hover:bg-spotify-surface/40 group"
            >
              <div>
                <div className="flex items-center justify-between mb-4">
                  <span className="text-[11px] font-bold uppercase tracking-wider text-spotify-green px-2.5 py-0.5 rounded-full bg-spotify-green/10 border border-spotify-green/20">
                    {insight.tag}
                  </span>
                  <div className="p-2 rounded-xl bg-spotify-surface border border-spotify-border group-hover:border-spotify-border-light transition-colors">
                    {iconMap[insight.icon] || <Sparkles className="w-5 h-5 text-spotify-green" />}
                  </div>
                </div>

                <h3 className="text-lg font-bold text-white mb-2 group-hover:text-spotify-green transition-colors">
                  {insight.title}
                </h3>
                <p className="text-xs text-spotify-muted leading-relaxed">
                  {insight.description}
                </p>
              </div>

              <div className="mt-6 pt-3 border-t border-spotify-border/40 text-[10px] text-spotify-subtext uppercase tracking-wider font-semibold">
                Regra: {insight.category}
              </div>
            </motion.div>
          ))}
        </div>
      )}
    </div>
  );
};

