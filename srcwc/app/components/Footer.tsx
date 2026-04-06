import { Heart, Instagram, Facebook, Mail } from "lucide-react";
import { FiligreeFlourish } from "./FiligreeFlourish";

export function Footer() {
  return (
    <footer className="bg-card/60 border-t border-primary/30 mt-20 neumorphic-outset">
      {/* Ornate top border */}
      <div className="h-px bg-gradient-to-r from-transparent via-primary to-transparent opacity-60" />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="text-center mb-8">
          <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60" />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Brand */}
          <div>
            <h3 className="text-2xl font-serif text-primary mb-4 tracking-wide text-glow-red">Wickedly Delightful</h3>
            <p className="text-foreground/70 leading-relaxed">
              Handcrafted wax melts with a gothic elegance. Each scent is carefully
              curated to create an atmosphere of mystery and luxury.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-lg font-serif text-foreground mb-4 tracking-wide">Quick Links</h4>
            <ul className="space-y-2">
              <li>
                <a href="/products" className="text-foreground/70 hover:text-primary transition-colors hover-glow">
                  Our Products
                </a>
              </li>
              <li>
                <a href="/about" className="text-foreground/70 hover:text-primary transition-colors hover-glow">
                  About Us
                </a>
              </li>
              <li>
                <a href="/contact" className="text-foreground/70 hover:text-primary transition-colors hover-glow">
                  Contact
                </a>
              </li>
            </ul>
          </div>

          {/* Social */}
          <div>
            <h4 className="text-lg font-serif text-foreground mb-4 tracking-wide">Follow Us</h4>
            <div className="flex space-x-4">
              <a
                href="#"
                className="w-10 h-10 bg-card border-2 border-primary/40 flex items-center justify-center transition-all hover-glow embossed"
              >
                <Instagram size={20} className="text-primary" />
              </a>
              <a
                href="#"
                className="w-10 h-10 bg-card border-2 border-primary/40 flex items-center justify-center transition-all hover-glow embossed"
              >
                <Facebook size={20} className="text-primary" />
              </a>
              <a
                href="#"
                className="w-10 h-10 bg-card border-2 border-primary/40 flex items-center justify-center transition-all hover-glow embossed"
              >
                <Mail size={20} className="text-primary" />
              </a>
            </div>
          </div>
        </div>

        <div className="mt-8 pt-8">
          <div className="h-px bg-gradient-to-r from-transparent via-primary/30 to-transparent mb-6" />
          <p className="text-foreground/60 flex items-center justify-center gap-2">
            Made with <Heart size={16} className="text-primary fill-primary pulse-glow" /> by Wickedly Delightful Scents © 2026
          </p>
        </div>
      </div>

      {/* Ornate bottom border */}
      <div className="h-px bg-gradient-to-r from-transparent via-primary to-transparent opacity-60" />
    </footer>
  );
}
