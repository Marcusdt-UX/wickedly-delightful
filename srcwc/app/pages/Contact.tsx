import { Mail, MapPin, Phone, Send } from "lucide-react";
import { FiligreeFlourish } from "../components/FiligreeFlourish";
import { LeatherTexture } from "../components/GrainTexture";
import { useState } from "react";

export function Contact() {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    message: "",
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    alert("Thank you for your message! We'll get back to you soon.");
    setFormData({ name: "", email: "", message: "" });
  };

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-background via-card to-background">
      {/* Header */}
      <section className="relative py-20 px-4 bg-gradient-to-b from-background to-transparent overflow-hidden">
        <LeatherTexture />
        
        <div className="max-w-7xl mx-auto text-center relative z-10">
          <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
          <h1 className="text-5xl md:text-6xl font-serif mb-6 text-foreground debossed-text tracking-luxury">
            Get in <span className="text-primary text-glow-red">Touch</span>
          </h1>
          <p className="text-xl text-foreground/70 max-w-2xl mx-auto leading-relaxed">
            We'd love to hear from you. Send us a message and we'll respond as soon as possible.
          </p>
          <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
        </div>
      </section>

      {/* Contact Section */}
      <section className="py-12 px-4">
        <div className="max-w-6xl mx-auto">
          <div className="grid md:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div className="ornate-card p-8">
              {/* Ornate corners */}
              <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60" />
              <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60" />
              <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60" />
              <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60" />

              <div className="relative z-10">
                <div className="pb-4 mb-6">
                  <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mb-3" />
                  <h2 className="text-3xl font-serif text-foreground tracking-wide">
                    Send Us a <span className="text-primary">Message</span>
                  </h2>
                  <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mt-3" />
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                  <div>
                    <label
                      htmlFor="name"
                      className="block text-foreground/80 mb-2 font-serif tracking-wide"
                    >
                      Name
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 bg-background border border-primary/30 focus:border-primary text-foreground placeholder-foreground/40 outline-none transition-colors input-neumorphic"
                      placeholder="Your name"
                    />
                  </div>

                  <div>
                    <label
                      htmlFor="email"
                      className="block text-foreground/80 mb-2 font-serif tracking-wide"
                    >
                      Email
                    </label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 bg-background border border-primary/30 focus:border-primary text-foreground placeholder-foreground/40 outline-none transition-colors input-neumorphic"
                      placeholder="your.email@example.com"
                    />
                  </div>

                  <div>
                    <label
                      htmlFor="message"
                      className="block text-foreground/80 mb-2 font-serif tracking-wide"
                    >
                      Message
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      value={formData.message}
                      onChange={handleChange}
                      required
                      rows={6}
                      className="w-full px-4 py-3 bg-background border border-primary/30 focus:border-primary text-foreground placeholder-foreground/40 outline-none transition-colors resize-none input-neumorphic"
                      placeholder="Tell us what's on your mind..."
                    />
                  </div>

                  <button
                    type="submit"
                    className="w-full px-6 py-4 bg-card text-primary border-2 border-primary hover:bg-primary hover:text-foreground transition-all duration-300 flex items-center justify-center gap-2 font-serif tracking-luxury btn-neumorphic"
                  >
                    <Send size={20} />
                    Send Message
                  </button>
                </form>
              </div>
            </div>

            {/* Contact Info */}
            <div className="space-y-8">
              <div className="ornate-card p-8">
                {/* Ornate corners */}
                <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60" />
                <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60" />
                <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60" />
                <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60" />

                <div className="relative z-10">
                  <div className="pb-4 mb-6">
                    <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mb-3" />
                    <h2 className="text-3xl font-serif text-foreground tracking-wide">
                      Contact <span className="text-primary">Information</span>
                    </h2>
                    <div className="w-20 h-px bg-gradient-to-r from-primary to-transparent mt-3" />
                  </div>

                  <div className="space-y-6">
                    <div className="flex items-start gap-4">
                      <div className="w-12 h-12 bg-card border-2 border-primary/40 flex items-center justify-center flex-shrink-0 embossed">
                        <Mail className="text-primary" size={24} />
                      </div>
                      <div>
                        <h3 className="text-lg font-serif text-foreground mb-1">Email</h3>
                        <p className="text-foreground/70">contact@wickedlydelightful.com</p>
                      </div>
                    </div>

                    <div className="flex items-start gap-4">
                      <div className="w-12 h-12 bg-card border-2 border-primary/40 flex items-center justify-center flex-shrink-0 embossed">
                        <Phone className="text-primary" size={24} />
                      </div>
                      <div>
                        <h3 className="text-lg font-serif text-foreground mb-1">Phone</h3>
                        <p className="text-foreground/70">+1 (555) 123-4567</p>
                      </div>
                    </div>

                    <div className="flex items-start gap-4">
                      <div className="w-12 h-12 bg-card border-2 border-primary/40 flex items-center justify-center flex-shrink-0 embossed">
                        <MapPin className="text-primary" size={24} />
                      </div>
                      <div>
                        <h3 className="text-lg font-serif text-foreground mb-1">Location</h3>
                        <p className="text-foreground/70">
                          123 Gothic Lane<br />
                          Velvet City, VC 12345
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="ornate-card p-8">
                {/* Ornate corners */}
                <div className="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-primary opacity-60" />
                <div className="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-primary opacity-60" />
                <div className="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-primary opacity-60" />
                <div className="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-primary opacity-60" />

                <div className="relative z-10">
                  <h3 className="text-2xl font-serif text-foreground mb-4 tracking-wide">
                    Business <span className="text-primary">Hours</span>
                  </h3>
                  <div className="space-y-2 text-foreground/70">
                    <div className="flex justify-between py-2">
                      <div className="w-16 h-px bg-gradient-to-r from-primary/40 to-transparent" />
                    </div>
                    <div className="flex justify-between py-2">
                      <span>Monday - Friday</span>
                      <span>9:00 AM - 6:00 PM</span>
                    </div>
                    <div className="w-full h-px bg-primary/20" />
                    <div className="flex justify-between py-2">
                      <span>Saturday</span>
                      <span>10:00 AM - 4:00 PM</span>
                    </div>
                    <div className="w-full h-px bg-primary/20" />
                    <div className="flex justify-between py-2">
                      <span>Sunday</span>
                      <span>Closed</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Additional Info */}
      <section className="relative py-16 px-4 mt-12 bg-gradient-to-b from-transparent to-background overflow-hidden">
        <LeatherTexture className="opacity-50" />
        
        <div className="max-w-4xl mx-auto text-center relative z-10">
          <FiligreeFlourish className="w-full max-w-md mx-auto h-16 text-primary opacity-60 mb-6" />
          <h2 className="text-3xl font-serif mb-6 text-foreground debossed-text tracking-wide">
            Follow Our <span className="text-primary text-glow-red">Journey</span>
          </h2>
          <p className="text-foreground/70 leading-relaxed">
            Stay connected with us on social media for the latest updates,
            new scent releases, and exclusive offers. Join our community
            of gothic fragrance enthusiasts.
          </p>
          <FiligreeFlourish className="w-full max-w-sm mx-auto h-12 text-primary opacity-40 mt-6" />
        </div>
      </section>
    </div>
  );
}
