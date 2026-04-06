export function FiligreeFlourish({ className = "", animate = false }: { className?: string; animate?: boolean }) {
  return (
    <svg
      className={`${className} ${animate ? 'filigree-draw' : ''}`}
      viewBox="0 0 400 100"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient id="filigreeGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stopColor="currentColor" stopOpacity="0" />
          <stop offset="50%" stopColor="currentColor" stopOpacity="1" />
          <stop offset="100%" stopColor="currentColor" stopOpacity="0" />
        </linearGradient>
      </defs>
      
      {/* Central flourish */}
      <path
        d="M 200 50 Q 180 30, 160 50 Q 180 70, 200 50 Q 220 30, 240 50 Q 220 70, 200 50"
        stroke="url(#filigreeGrad)"
        strokeWidth="1.5"
        fill="none"
        className={animate ? 'draw-path' : ''}
      />
      
      {/* Left side decoration */}
      <path
        d="M 160 50 Q 140 45, 120 50 Q 100 55, 80 50 Q 60 45, 40 50 Q 20 55, 0 50"
        stroke="url(#filigreeGrad)"
        strokeWidth="1.2"
        fill="none"
        className={animate ? 'draw-path' : ''}
        style={{ animationDelay: '0.2s' }}
      />
      
      {/* Right side decoration */}
      <path
        d="M 240 50 Q 260 45, 280 50 Q 300 55, 320 50 Q 340 45, 360 50 Q 380 55, 400 50"
        stroke="url(#filigreeGrad)"
        strokeWidth="1.2"
        fill="none"
        className={animate ? 'draw-path' : ''}
        style={{ animationDelay: '0.2s' }}
      />
      
      {/* Decorative dots */}
      <circle cx="200" cy="50" r="4" fill="currentColor" opacity="0.8" />
      <circle cx="160" cy="50" r="3" fill="currentColor" opacity="0.6" />
      <circle cx="240" cy="50" r="3" fill="currentColor" opacity="0.6" />
      <circle cx="120" cy="50" r="2.5" fill="currentColor" opacity="0.5" />
      <circle cx="280" cy="50" r="2.5" fill="currentColor" opacity="0.5" />
      <circle cx="80" cy="50" r="2" fill="currentColor" opacity="0.4" />
      <circle cx="320" cy="50" r="2" fill="currentColor" opacity="0.4" />
    </svg>
  );
}
