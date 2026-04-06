export function LacePattern({ className = "" }: { className?: string }) {
  return (
    <svg
      className={className}
      viewBox="0 0 1200 100"
      preserveAspectRatio="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <pattern
          id="lace-pattern"
          x="0"
          y="0"
          width="60"
          height="100"
          patternUnits="userSpaceOnUse"
        >
          {/* Lace scallop pattern */}
          <path
            d="M 0 50 Q 15 30, 30 50 Q 45 70, 60 50"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.5"
            opacity="0.6"
          />
          <circle cx="30" cy="50" r="3" fill="currentColor" opacity="0.4" />
          <circle cx="15" cy="40" r="2" fill="currentColor" opacity="0.3" />
          <circle cx="45" cy="40" r="2" fill="currentColor" opacity="0.3" />
        </pattern>
      </defs>
      <rect width="1200" height="100" fill="url(#lace-pattern)" />
    </svg>
  );
}

export function LaceBorder({ position = "top" }: { position?: "top" | "bottom" }) {
  return (
    <div
      className={`absolute left-0 right-0 w-full h-16 text-primary/40 pointer-events-none ${
        position === "top" ? "top-0" : "bottom-0"
      } ${position === "bottom" ? "rotate-180" : ""}`}
    >
      <LacePattern className="w-full h-full" />
    </div>
  );
}
