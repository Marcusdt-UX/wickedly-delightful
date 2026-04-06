import { Heart, Sparkles, Moon } from "lucide-react";
import { FiligreeFlourish } from "../components/FiligreeFlourish";
import { LeatherTexture } from "../components/GrainTexture";
import { ImageWithFallback } from "../components/figma/ImageWithFallback";

export function About() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-background via-card to-background">
      {/* Header */}
      <section className="relative py-20 px-4 bg-gradient-to-b from-background to-transparent overflow-hidden">
        <LeatherTexture />
        
        <div className="max-w-7xl mx-auto text-center relative z-10">
          <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
          <h1 className="text-5xl md:text-6xl font-serif mb-6 text-foreground debossed-text tracking-luxury">
            Our <span className="text-primary text-glow-red">Story</span>
          </h1>
          <p className="text-xl text-foreground/70 max-w-2xl mx-auto leading-relaxed">
            Where darkness meets elegance, and every scent tells a story
          </p>
          <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
        </div>
      </section>

      {/* Story Section */}
      <section className="py-12 px-4">
        <div className="max-w-4xl mx-auto">
          <div className="grid md:grid-cols-2 gap-8 items-center mb-16">
            <div className="relative ornate-card overflow-hidden">
              {/* Ornate corners */}
              <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60 z-10" />

              <ImageWithFallback
                src="https://images.unsplash.com/photo-1585641689080-2e530457803b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzY2VudGVkJTIwd2F4JTIwY2FuZGxlJTIwYnVybmluZ3xlbnwxfHx8fDE3NzQ3MjMzMjl8MA&ixlib=rb-4.1.0&q=80&w=1080"
                alt="Burning candles"
                className="w-full h-96 object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
            </div>

            <div className="space-y-4">
              <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mb-4" />
              <h2 className="text-3xl font-serif text-foreground mb-4 tracking-wide">
                Born from <span className="text-primary text-glow-red">Passion</span>
              </h2>
              <p className="text-foreground/80 leading-relaxed">
                Wickedly Delightful Scents began with a simple love for the mysterious and beautiful.
                We believe that fragrance should be more than just a pleasant scent—it
                should be an experience, a journey into the depths of elegance and allure.
              </p>
              <p className="text-foreground/80 leading-relaxed">
                Each wax melt is handcrafted with care, combining premium soy wax with
                carefully selected fragrances that evoke the gothic aesthetic we hold dear.
              </p>
              <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mt-4" />
            </div>
          </div>

          {/* Values */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
              <h2 className="text-3xl font-serif text-foreground debossed-text tracking-wide">
                Our <span className="text-primary">Values</span>
              </h2>
            </div>

            <div className="grid md:grid-cols-3 gap-8">
              <div className="ornate-card p-8 text-center">
                {/* Ornate corners */}
                <div className="absolute top-2 left-2 w-10 h-10 border-t-2 border-l-2 border-primary opacity-60" />
                <div className="absolute top-2 right-2 w-10 h-10 border-t-2 border-r-2 border-primary opacity-60" />
                <div className="absolute bottom-2 left-2 w-10 h-10 border-b-2 border-l-2 border-primary opacity-60" />
                <div className="absolute bottom-2 right-2 w-10 h-10 border-b-2 border-r-2 border-primary opacity-60" />

                <div className="relative z-10">
                  <div className="w-16 h-16 mx-auto mb-4 bg-card border-2 border-primary/40 flex items-center justify-center embossed">
                    <Heart className="text-primary" size={32} />
                  </div>
                  <h3 className="text-xl font-serif mb-3 text-foreground tracking-wide">Handcrafted</h3>
                  <p className="text-foreground/70 leading-relaxed">
                    Every piece is made by hand with love and attention to detail
                  </p>
                </div>
              </div>

              <div className="ornate-card p-8 text-center">
                {/* Ornate corners */}
                <div className="absolute top-2 left-2 w-10 h-10 border-t-2 border-l-2 border-primary opacity-60" />
                <div className="absolute top-2 right-2 w-10 h-10 border-t-2 border-r-2 border-primary opacity-60" />
                <div className="absolute bottom-2 left-2 w-10 h-10 border-b-2 border-l-2 border-primary opacity-60" />
                <div className="absolute bottom-2 right-2 w-10 h-10 border-b-2 border-r-2 border-primary opacity-60" />

                <div className="relative z-10">
                  <div className="w-16 h-16 mx-auto mb-4 bg-card border-2 border-primary/40 flex items-center justify-center embossed">
                    <Sparkles className="text-primary" size={32} />
                  </div>
                  <h3 className="text-xl font-serif mb-3 text-foreground tracking-wide">Premium Quality</h3>
                  <p className="text-foreground/70 leading-relaxed">
                    We use only the finest soy wax and phthalate-free fragrance oils
                  </p>
                </div>
              </div>

              <div className="ornate-card p-8 text-center">
                {/* Ornate corners */}
                <div className="absolute top-2 left-2 w-10 h-10 border-t-2 border-l-2 border-primary opacity-60" />
                <div className="absolute top-2 right-2 w-10 h-10 border-t-2 border-r-2 border-primary opacity-60" />
                <div className="absolute bottom-2 left-2 w-10 h-10 border-b-2 border-l-2 border-primary opacity-60" />
                <div className="absolute bottom-2 right-2 w-10 h-10 border-b-2 border-r-2 border-primary opacity-60" />

                <div className="relative z-10">
                  <div className="w-16 h-16 mx-auto mb-4 bg-card border-2 border-primary/40 flex items-center justify-center embossed">
                    <Moon className="text-primary" size={32} />
                  </div>
                  <h3 className="text-xl font-serif mb-3 text-foreground tracking-wide">Gothic Aesthetic</h3>
                  <p className="text-foreground/70 leading-relaxed">
                    Each scent is inspired by the beauty of darkness and mystery
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Mission */}
          <div className="relative ornate-card p-12 overflow-hidden">
            {/* Ornate corners */}
            <div className="absolute top-2 left-2 w-16 h-16 border-t-2 border-l-2 border-primary opacity-60" />
            <div className="absolute top-2 right-2 w-16 h-16 border-t-2 border-r-2 border-primary opacity-60" />
            <div className="absolute bottom-2 left-2 w-16 h-16 border-b-2 border-l-2 border-primary opacity-60" />
            <div className="absolute bottom-2 right-2 w-16 h-16 border-b-2 border-r-2 border-primary opacity-60" />
            
            <div className="relative z-10">
              <div className="text-center mb-6">
                <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
                <h2 className="text-3xl font-serif text-foreground debossed-text tracking-wide">
                  Our <span className="text-primary text-glow-red">Mission</span>
                </h2>
              </div>
              <p className="text-foreground/80 text-center leading-relaxed max-w-2xl mx-auto mb-4">
                To bring the beauty of gothic elegance into everyday life through handcrafted
                wax melts that inspire, captivate, and transport you to a world of mystery
                and romance.
              </p>
              <p className="text-foreground/70 text-center italic">
                "In every melt, we capture the essence of darkness and light intertwined"
              </p>
              <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
            </div>
          </div>
        </div>
      </section>

      {/* Image Gallery */}
      <section className="relative py-16 px-4 overflow-hidden">
        <LeatherTexture className="opacity-50" />
        
        <div className="max-w-7xl mx-auto relative z-10">
          <div className="text-center mb-12">
            <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
            <h2 className="text-3xl font-serif text-foreground debossed-text tracking-wide">
              Our <span className="text-primary text-glow-red">Craft</span>
            </h2>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="relative h-80 ornate-card overflow-hidden group">
              {/* Ornate corners */}
              <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60 z-10" />

              <ImageWithFallback
                src="https://images.unsplash.com/photo-1707839568431-c2648f6d5184?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxhcm9tYXRoZXJhcHklMjBjYW5kbGVzJTIwbHV4dXJ5fGVufDF8fHx8MTc3NDcyMzMyOXww&ixlib=rb-4.1.0&q=80&w=1080"
                alt="Aromatherapy candles"
                className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
            </div>
            
            <div className="relative h-80 ornate-card overflow-hidden group">
              {/* Ornate corners */}
              <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60 z-10" />

              <ImageWithFallback
                src="https://images.unsplash.com/photo-1629606046986-2203dc763b59?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnb3RoaWMlMjBjYW5kbGVzJTIwZGFya3xlbnwxfHx8fDE3NzQ3MjMzMzB8MA&ixlib=rb-4.1.0&q=80&w=1080"
                alt="Gothic candles"
                className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
