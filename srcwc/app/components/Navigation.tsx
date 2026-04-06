import { Link, useLocation } from "react-router";
import { Menu, X } from "lucide-react";
import { useState } from "react";
import logoImage from "figma:asset/1cabec8e003a8b0f7b467258de2ca8cf5dcc4fa5.png";

export function Navigation() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const location = useLocation();

  const navLinks = [
    { path: "/", label: "Home" },
    { path: "/products", label: "Products" },
    { path: "/about", label: "About" },
    { path: "/contact", label: "Contact" },
  ];

  const isActive = (path: string) => {
    if (path === "/") {
      return location.pathname === "/";
    }
    return location.pathname.startsWith(path);
  };

  return (
    <nav className="border-b border-primary/30 bg-background/95 backdrop-blur-sm sticky top-0 z-50 neumorphic-outset">
      {/* Ornate top border */}
      <div className="h-px bg-gradient-to-r from-transparent via-primary to-transparent opacity-60" />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-20">
          {/* Logo */}
          <Link to="/" className="flex items-center group">
            <img 
              src={logoImage} 
              alt="Wickedly Delightful Scents" 
              className="h-16 w-16 transition-all group-hover:drop-shadow-[0_0_15px_rgba(155,17,30,0.6)]"
            />
            <div className="ml-3 hidden sm:block">
              <h1 className="text-xl font-serif tracking-wide text-primary group-hover:text-accent transition-colors text-glow-red">
                Wickedly Delightful
              </h1>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex space-x-8">
            {navLinks.map((link) => (
              <Link
                key={link.path}
                to={link.path}
                className={`relative py-2 px-4 transition-all font-serif tracking-wider hover-glow ${
                  isActive(link.path)
                    ? "text-primary"
                    : "text-foreground/80 hover:text-primary"
                }`}
              >
                {link.label}
                {isActive(link.path) && (
                  <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-primary glow-red" />
                )}
              </Link>
            ))}
          </div>

          {/* Mobile menu button */}
          <button
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            className="md:hidden p-2 text-foreground hover:text-primary transition-colors hover-glow"
          >
            {isMenuOpen ? <X size={24} /> : <Menu size={24} />}
          </button>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <div className="md:hidden pb-4 border-t border-primary/30 mt-2">
            <div className="flex flex-col space-y-2 pt-4">
              {navLinks.map((link) => (
                <Link
                  key={link.path}
                  to={link.path}
                  onClick={() => setIsMenuOpen(false)}
                  className={`py-2 px-4 transition-colors font-serif tracking-wide ${
                    isActive(link.path)
                      ? "text-primary bg-primary/10 neumorphic-inset"
                      : "text-foreground/80 hover:text-primary hover:bg-primary/5"
                  }`}
                >
                  {link.label}
                </Link>
              ))}
            </div>
          </div>
        )}
      </div>

      {/* Ornate bottom border */}
      <div className="h-px bg-gradient-to-r from-transparent via-primary to-transparent opacity-60" />
    </nav>
  );
}
