import React from 'react';
import { LucideIcon } from 'lucide-react';

interface StatCardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  icon: LucideIcon;
  badge?: string;
  highlight?: boolean;
}

export const StatCard: React.FC<StatCardProps> = ({
  title,
  value,
  subtitle,
  icon: Icon,
  badge,
  highlight = false,
}) => {
  return (
    <div
      className={`relative overflow-hidden rounded-2xl border p-5 transition-all duration-300 hover:border-spotify-border-light group ${
        highlight
          ? 'border-spotify-green/40 bg-gradient-to-br from-spotify-green/10 via-spotify-dark/80 to-spotify-dark glow-subtle'
          : 'border-spotify-border bg-spotify-dark/80'
      }`}
    >
      <div className="flex items-center justify-between mb-3">
        <span className="text-xs uppercase tracking-wider font-medium text-spotify-muted">
          {title}
        </span>
        <div
          className={`p-2.5 rounded-xl transition-colors ${
            highlight
              ? 'bg-spotify-green/20 text-spotify-green'
              : 'bg-spotify-surface text-spotify-muted group-hover:text-white'
          }`}
        >
          <Icon className="w-4 h-4" />
        </div>
      </div>

      <div className="flex items-baseline gap-2">
        <div className="text-2xl lg:text-3xl font-bold text-white tracking-tight">
          {value}
        </div>
        {badge && (
          <span className="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-spotify-green/20 text-spotify-green border border-spotify-green/30">
            {badge}
          </span>
        )}
      </div>

      {subtitle && (
        <p className="mt-2 text-xs text-spotify-muted line-clamp-1">{subtitle}</p>
      )}
    </div>
  );
};

