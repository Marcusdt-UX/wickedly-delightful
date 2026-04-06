export function GrainTexture() {
  return (
    <div className="fixed inset-0 pointer-events-none z-50 opacity-[0.015]">
      <svg className="w-full h-full">
        <filter id="noise">
          <feTurbulence
            type="fractalNoise"
            baseFrequency="0.9"
            numOctaves="4"
            stitchTiles="stitch"
          />
          <feColorMatrix type="saturate" values="0" />
        </filter>
        <rect width="100%" height="100%" filter="url(#noise)" />
      </svg>
    </div>
  );
}

export function LeatherTexture({ className = "" }: { className?: string }) {
  return (
    <div className={`absolute inset-0 pointer-events-none ${className}`}>
      <div
        className="w-full h-full opacity-[0.03]"
        style={{
          backgroundImage: `
            radial-gradient(circle at 20% 30%, rgba(155, 17, 30, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(155, 17, 30, 0.3) 0%, transparent 50%),
            repeating-linear-gradient(
              90deg,
              transparent,
              transparent 2px,
              rgba(0, 0, 0, 0.1) 2px,
              rgba(0, 0, 0, 0.1) 4px
            )
          `,
        }}
      />
    </div>
  );
}
