import { Link } from "react-router";
import { Flame, Sparkles, Heart } from "lucide-react";
import { FiligreeFlourish } from "../components/FiligreeFlourish";
import { LeatherTexture } from "../components/GrainTexture";
import { ImageWithFallback } from "../components/figma/ImageWithFallback";
import { useEffect, useState } from "react";
import logoImage from "figma:asset/1cabec8e003a8b0f7b467258de2ca8cf5dcc4fa5.png";

export function Home() {
  const [scrollY, setScrollY] = useState(0);

  useEffect(() => {
    const handleScroll = () => {
      setScrollY(window.scrollY);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const features = [
    {
      icon: Flame,
      title: "Handcrafted",
      description: "Each wax melt is carefully crafted by hand with premium ingredients",
    },
    {
      icon: Sparkles,
      title: "Unique Scents",
      description: "Gothic-inspired fragrances that captivate and enchant",
    },
    {
      icon: Heart,
      title: "Made with Love",
      description: "Every piece is created with passion and attention to detail",
    },
  ];

  return (
    <div className="min-h-screen">
      {/* Hero Section with Parallax */}
      <section className="relative min-h-[700px] flex items-center justify-center overflow-hidden bg-gradient-to-b from-background via-[#0D0D0D] to-background parallax-container">
        <LeatherTexture />
        
        {/* Background Image with parallax */}
        <div 
          className="absolute inset-0 opacity-20"
          style={{ transform: `translateY(${scrollY * 0.5}px)` }}
        >
          <ImageWithFallback
            src="https://images.unsplash.com/photo-1762190674294-58e4a7a40a35?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx3YXglMjBtZWx0cyUyMGNhbmRsZXMlMjBkYXJrfGVufDF8fHx8MTc3NDcyMzMyOXww&ixlib=rb-4.1.0&q=80&w=1080"
            alt="Wax melts background"
            className="w-full h-full object-cover"
          />
        </div>

        {/* Animated Filigree - top */}
        <div 
          className="absolute top-20 left-1/2 -translate-x-1/2 w-full max-w-2xl text-primary opacity-50"
          style={{ transform: `translate(-50%, ${scrollY * 0.2}px)` }}
        >
          <FiligreeFlourish animate className="w-full h-20" />
        </div>

        {/* Hero Content */}
        <div className="relative z-10 text-center px-4 max-w-4xl mx-auto">
          {/* Logo - stays fixed */}
          <div className="mb-8 flex justify-center">
            <img 
              src={logoImage} 
              alt="Wickedly Delightful Scents" 
              className="w-48 h-48 pulse-glow"
            />
          </div>

          <h1 className="text-5xl md:text-7xl font-serif mb-6 text-foreground tracking-luxury debossed-text">
            Wickedly <span className="text-primary text-glow-red">Delightful</span>
          </h1>
          
          <div className="mb-8">
            <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60" />
          </div>

          <p className="text-xl md:text-2xl mb-8 text-foreground/90 tracking-wide font-serif">
            Wickedly Delightful Scents
          </p>
          <p className="text-lg mb-10 text-foreground/70 max-w-2xl mx-auto leading-relaxed">
            Immerse yourself in the dark allure of our handcrafted wax melts.
            Each scent tells a story of mystery, romance, and timeless elegance.
          </p>
          <Link
            to="/products"
            className="inline-block px-10 py-4 bg-card text-primary border-2 border-primary hover:bg-primary hover:text-foreground transition-all duration-300 font-serif tracking-luxury btn-neumorphic"
          >
            Explore Our Collection
          </Link>
        </div>

        {/* Animated Filigree - bottom */}
        <div 
          className="absolute bottom-20 left-1/2 -translate-x-1/2 w-full max-w-2xl text-primary opacity-50 rotate-180"
          style={{ transform: `translate(-50%, 0) rotate(180deg) translateY(${scrollY * 0.2}px)` }}
        >
          <FiligreeFlourish animate className="w-full h-20" />
        </div>
      </section>

      {/* Features Section */}
      <section className="relative py-20 px-4 bg-gradient-to-b from-background to-card">
        <LeatherTexture className="opacity-50" />
        
        <div className="max-w-7xl mx-auto relative z-10">
          {/* Section Header */}
          <div className="text-center mb-16">
            <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
            <h2 className="text-4xl font-serif mb-4 text-foreground debossed-text tracking-wide">
              Why Choose <span className="text-primary text-glow-red">Wickedly Delightful</span>
            </h2>
            <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {features.map((feature, index) => {
              const Icon = feature.icon;
              return (
                <div
                  key={index}
                  className="relative p-8 ornate-card group fade-in-up"
                  style={{ animationDelay: `${index * 0.2}s` }}
                >
                  {/* Ornate corner decorations */}
                  <div className="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-primary opacity-60" />
                  <div className="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-primary opacity-60" />
                  <div className="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-primary opacity-60" />
                  <div className="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-primary opacity-60" />

                  <div className="flex flex-col items-center text-center relative z-10">
                    <div className="w-16 h-16 mb-4 bg-card border-2 border-primary/40 flex items-center justify-center group-hover:glow-red transition-all duration-300 embossed">
                      <Icon className="text-primary" size={32} />
                    </div>
                    <h3 className="text-xl font-serif mb-3 text-foreground tracking-wide">
                      {feature.title}
                    </h3>
                    <p className="text-foreground/70 leading-relaxed">
                      {feature.description}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Featured Products Preview */}
      <section className="relative py-20 px-4 bg-gradient-to-b from-card to-background overflow-hidden">
        <LeatherTexture />
        
        <div className="max-w-7xl mx-auto relative z-10">
          <div className="text-center mb-12">
            <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
            <h2 className="text-4xl font-serif mb-4 text-foreground debossed-text tracking-wide">
              Featured <span className="text-primary text-glow-red">Collections</span>
            </h2>
            <p className="text-foreground/70 max-w-2xl mx-auto leading-relaxed">
              Discover our signature scents, inspired by the beauty of darkness
            </p>
            <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div className="relative group overflow-hidden ornate-card">
              {/* Ornate corners */}
              <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60 z-10" />

              <ImageWithFallback
                src="https://images.unsplash.com/photo-1629606046986-2203dc763b59?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnb3RoaWMlMjBjYW5kbGVzJTIwZGFya3xlbnwxfHx8fDE3NzQ3MjMzMzB8MA&ixlib=rb-4.1.0&q=80&w=1080"
                alt="Gothic candles"
                className="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent flex items-end p-6">
                <div>
                  <h3 className="text-2xl font-serif text-foreground mb-2 tracking-wide text-glow-red">
                    Midnight Rose
                  </h3>
                  <p className="text-foreground/80">Dark florals with a hint of mystery</p>
                </div>
              </div>
            </div>

            <div className="relative group overflow-hidden ornate-card">
              {/* Ornate corners */}
              <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60 z-10" />
              <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60 z-10" />

              <ImageWithFallback
                src="https://images.unsplash.com/photo-1767858698621-6e901c7c5afb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxyZWQlMjByb3NlcyUyMGRhcmslMjBnb3RoaWN8ZW58MXx8fHwxNzc0NzIzMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080"
                alt="Red roses"
                className="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent flex items-end p-6">
                <div>
                  <h3 className="text-2xl font-serif text-foreground mb-2 tracking-wide text-glow-red">
                    Velvet Dreams
                  </h3>
                  <p className="text-foreground/80">Rich, luxurious, and captivating</p>
                </div>
              </div>
            </div>
          </div>

          <div className="text-center">
            <Link
              to="/products"
              className="inline-block px-8 py-3 bg-card border-2 border-primary text-primary hover:bg-primary hover:text-foreground transition-all duration-300 font-serif tracking-luxury btn-neumorphic"
            >
              View All Products
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
