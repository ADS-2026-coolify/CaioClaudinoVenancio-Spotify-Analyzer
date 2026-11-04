import React from 'react';
import type { TimeRange } from '@/types';

interface TimeRangeSelectorProps {
  value: TimeRange;
  onChange: (value: TimeRange) => void;
}

const ranges: { value: TimeRange; label: string }[] = [
  { value: 'short_term', label: 'Últimas 4 semanas' },
  { value: 'medium_term', label: 'Últimos 6 meses' },
  { value: 'long_term', label: 'Todos os tempos' },
];

export const TimeRangeSelector: React.FC<TimeRangeSelectorProps> = ({ value, onChange }) => {
  return (
    <div className="inline-flex p-1 rounded-xl bg-spotify-surface border border-spotify-border/80">
      {ranges.map((range) => {
        const isActive = value === range.value;
        return (
          <button
            key={range.value}
            onClick={() => onChange(range.value)}
            className={`px-3.5 py-1.5 rounded-lg text-xs md:text-sm font-medium transition-all duration-200 ${
              isActive
                ? 'bg-spotify-green text-black font-semibold shadow-sm'
                : 'text-spotify-muted hover:text-white hover:bg-white/5'
            }`}
          >
            {range.label}
          </button>
        );
      })}
    </div>
  );
};

