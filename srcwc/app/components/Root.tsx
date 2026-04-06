import { Outlet } from "react-router";
import { Navigation } from "./Navigation";
import { Footer } from "./Footer";
import { GrainTexture } from "./GrainTexture";

export function Root() {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <GrainTexture />
      <Navigation />
      <main className="flex-1">
        <Outlet />
      </main>
      <Footer />
    </div>
  );
}
