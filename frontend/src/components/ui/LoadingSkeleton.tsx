import React from 'react';

interface LoadingSkeletonProps {
  className?: string;
  count?: number;
}

export const LoadingSkeleton: React.FC<LoadingSkeletonProps> = ({ className = 'h-6 w-full', count = 1 }) => {
  return (
    <>
      {Array.from({ length: count }).map((_, index) => (
        <div
          key={index}
          className={`animate-pulse rounded-lg bg-spotify-surface ${className}`}
        />
      ))}
    </>
  );
};

export const CardSkeleton: React.FC<{ count?: number }> = ({ count = 3 }) => {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      {Array.from({ length: count }).map((_, index) => (
        <div
          key={index}
          className="rounded-2xl border border-spotify-border bg-spotify-dark/80 p-6 space-y-4"
        >
          <div className="flex items-center justify-between">
            <div className="h-4 w-28 bg-spotify-surface rounded animate-pulse" />
            <div className="h-8 w-8 bg-spotify-surface rounded-full animate-pulse" />
          </div>
          <div className="h-8 w-36 bg-spotify-surface rounded animate-pulse" />
          <div className="h-3 w-48 bg-spotify-surface rounded animate-pulse" />
        </div>
      ))}
    </div>
  );
};
