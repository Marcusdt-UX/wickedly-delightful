import { ShoppingCart, Flame } from "lucide-react";
import { FiligreeFlourish } from "../components/FiligreeFlourish";
import { LeatherTexture } from "../components/GrainTexture";
import { ImageWithFallback } from "../components/figma/ImageWithFallback";

export function Products() {
  const products = [
    {
      name: "Midnight Rose",
      description: "Dark rose petals with hints of blackberry and vanilla",
      price: "$12.00",
      image: "https://images.unsplash.com/photo-1767858698621-6e901c7c5afb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxyZWQlMjByb3NlcyUyMGRhcmslMjBnb3RoaWN8ZW58MXx8fHwxNzc0NzIzMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080",
    },
    {
      name: "Velvet Dreams",
      description: "Rich velvet with notes of amber and dark chocolate",
      price: "$12.00",
      image: "https://images.unsplash.com/photo-1762190674294-58e4a7a40a35?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx3YXglMjBtZWx0cyUyMGNhbmRsZXMlMjBkYXJrfGVufDF8fHx8MTc3NDcyMzMyOXww&ixlib=rb-4.1.0&q=80&w=1080",
    },
    {
      name: "Gothic Garden",
      description: "Dark florals with jasmine, lily, and sandalwood",
      price: "$12.00",
      image: "https://images.unsplash.com/photo-1629606046986-2203dc763b59?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnb3RoaWMlMjBjYW5kbGVzJTIwZGFya3xlbnwxfHx8fDE3NzQ3MjMzMzB8MA&ixlib=rb-4.1.0&q=80&w=1080",
    },
    {
      name: "Crimson Moon",
      description: "Blood orange, cinnamon, and clove with a smoky finish",
      price: "$12.00",
      image: "https://images.unsplash.com/photo-1585641689080-2e530457803b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzY2VudGVkJTIwd2F4JTIwY2FuZGxlJTIwYnVybmluZ3xlbnwxfHx8fDE3NzQ3MjMzMjl8MA&ixlib=rb-4.1.0&q=80&w=1080",
    },
    {
      name: "Shadow Orchid",
      description: "Exotic orchid with hints of patchouli and musk",
      price: "$12.00",
      image: "https://images.unsplash.com/photo-1707839568431-c2648f6d5184?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxhcm9tYXRoZXJhcHklMjBjYW5kbGVzJTIwbHV4dXJ5fGVufDF8fHx8MTc3NDcyMzMyOXww&ixlib=rb-4.1.0&q=80&w=1080",
    },
    {
      name: "Dark Enchantment",
      description: "Mysterious blend of bergamot, cedar, and black tea",
      price: "$12.00",
      image: "https://images.unsplash.com/photo-1639155284894-b905ae1f9761?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxibGFjayUyMGxhY2UlMjB0ZXh0dXJlJTIwcGF0dGVybnxlbnwxfHx8fDE3NzQ3MjMzMzB8MA&ixlib=rb-4.1.0&q=80&w=1080",
    },
  ];

  return (
    <div className="min-h-screen bg-gradient-to-b from-background via-card to-background">
      {/* Header */}
      <section className="relative py-20 px-4 bg-gradient-to-b from-background to-transparent overflow-hidden">
        <LeatherTexture />
        
        <div className="max-w-7xl mx-auto text-center relative z-10">
          <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
          <h1 className="text-5xl md:text-6xl font-serif mb-6 text-foreground debossed-text tracking-luxury">
            Our <span className="text-primary text-glow-red">Collection</span>
          </h1>
          <p className="text-xl text-foreground/70 max-w-2xl mx-auto leading-relaxed">
            Handcrafted wax melts infused with gothic elegance and mystery.
            Each scent is carefully curated to transport you to a world of dark beauty.
          </p>
          <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
        </div>
      </section>

      {/* Products Grid */}
      <section className="py-12 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {products.map((product, index) => (
              <div
                key={index}
                className="group ornate-card overflow-hidden fade-in-up"
                style={{ animationDelay: `${index * 0.1}s` }}
              >
                {/* Ornate corners */}
                <div className="absolute top-2 left-2 w-10 h-10 border-t-2 border-l-2 border-primary opacity-60 z-10" />
                <div className="absolute top-2 right-2 w-10 h-10 border-t-2 border-r-2 border-primary opacity-60 z-10" />
                <div className="absolute bottom-2 left-2 w-10 h-10 border-b-2 border-l-2 border-primary opacity-60 z-10" />
                <div className="absolute bottom-2 right-2 w-10 h-10 border-b-2 border-r-2 border-primary opacity-60 z-10" />

                {/* Product Image */}
                <div className="relative overflow-hidden h-64">
                  <ImageWithFallback
                    src={product.image}
                    alt={product.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                  
                  {/* Flame icon overlay */}
                  <div className="absolute top-4 right-4 w-10 h-10 bg-primary/80 border border-primary flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity glow-red embossed">
                    <Flame size={20} className="text-foreground" />
                  </div>
                </div>

                {/* Product Info */}
                <div className="p-6 relative z-10">
                  <div className="pb-4 mb-4">
                    <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mb-3" />
                    <h3 className="text-2xl font-serif mb-2 text-foreground tracking-wide">
                      {product.name}
                    </h3>
                    <p className="text-foreground/70 leading-relaxed">
                      {product.description}
                    </p>
                    <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mt-3" />
                  </div>

                  <div className="flex items-center justify-between">
                    <span className="text-2xl font-serif text-primary text-glow-red">
                      {product.price}
                    </span>
                    <button className="px-4 py-2 bg-card border-2 border-primary text-primary hover:bg-primary hover:text-foreground transition-all duration-300 flex items-center gap-2 btn-neumorphic">
                      <ShoppingCart size={18} />
                      <span className="font-serif tracking-wide">Add</span>
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Info Section */}
      <section className="relative py-16 px-4 mt-12 bg-gradient-to-b from-transparent to-background overflow-hidden">
        <LeatherTexture className="opacity-50" />
        
        <div className="max-w-4xl mx-auto text-center relative z-10">
          <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
          <h2 className="text-3xl font-serif mb-6 text-foreground debossed-text tracking-wide">
            How to Use Our <span className="text-primary text-glow-red">Wax Melts</span>
          </h2>
          <p className="text-foreground/70 leading-relaxed mb-4">
            Simply place one or two wax melts in your warmer and enjoy the captivating aroma
            as it fills your space. Each melt provides hours of long-lasting fragrance.
          </p>
          <p className="text-foreground/60 italic">
            Made with premium soy wax and phthalate-free fragrance oils
          </p>
          <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
        </div>
      </section>
    </div>
  );
}
