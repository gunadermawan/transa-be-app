<x-filament-panels::page>
    <div style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
        <div style="max-width: 1024px; width: 100%; margin: 0 auto; padding: 0 1rem;">
            <div style="text-align: center;">
                <!-- Icon -->
                <div style="display: flex; justify-content: center; margin-bottom: 2rem;">
                    <div
                        style="background-color: rgba(59, 130, 246, 0.1); border-radius: 50%; padding: 1rem; display: inline-flex;">
                        <svg style="width: 48px; height: 48px; color: rgb(59, 130, 246);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <!-- Message -->
                <div style="margin-bottom: 2rem;">
                    <h2 style="font-size: 2rem; font-weight: bold; margin-bottom: 1rem; color: inherit;">
                        {{ $this->getHeading() }}
                    </h2>
                    <p style="font-size: 1.125rem; color: #6b7280; max-width: 42rem; margin: 0 auto;">
                        {{ $this->getSubheading() }}
                    </p>
                </div>

                <!-- CTA -->
                <div style="margin-bottom: 3rem;">
                    <a href="https://jagoflutter.com/academy/pos-saas/register" target="_blank"
                        style="display: inline-flex; align-items: center; padding: 0.875rem 2rem; background-color: rgb(59, 130, 246); color: white; font-weight: 600; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='rgb(37, 99, 235)'; this.style.transform='scale(1.05)';"
                        onmouseout="this.style.backgroundColor='rgb(59, 130, 246)'; this.style.transform='scale(1)';">
                        {{-- <svg style="width: 20px; height: 20px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg> --}}
                        Gabung Academy POS Sekarang
                    </a>
                    <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 1rem;">
                        Dapatkan akses ke semua fitur premium dan materi pembelajaran lengkap
                    </p>
                </div>

                <!-- Features Preview -->
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; max-width: 56rem; margin: 0 auto;">
                    <div
                        style="background: rgba(255, 255, 255, 0.05); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid rgba(229, 231, 235, 0.3); text-align: center;">
                        <div
                            style="display: inline-flex; background-color: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1rem;">
                            <svg style="width: 24px; height: 24px; color: rgb(59, 130, 246);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: inherit;">Live Session</h3>
                        <p style="font-size: 0.875rem; color: #9ca3af;">27 sesi live zoom, dapat video rekaman nya</p>
                    </div>

                    <div
                        style="background: rgba(255, 255, 255, 0.05); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid rgba(229, 231, 235, 0.3); text-align: center;">
                        <div
                            style="display: inline-flex; background-color: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1rem;">
                            <svg style="width: 24px; height: 24px; color: rgb(59, 130, 246);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: inherit;">Source Code</h3>
                        <p style="font-size: 0.875rem; color: #9ca3af;">Akses penuh ke source code lengkap</p>
                    </div>

                    <div
                        style="background: rgba(255, 255, 255, 0.05); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid rgba(229, 231, 235, 0.3); text-align: center;">
                        <div
                            style="display: inline-flex; background-color: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1rem;">
                            <svg style="width: 24px; height: 24px; color: rgb(59, 130, 246);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: inherit;">Community Support</h3>
                        <p style="font-size: 0.875rem; color: #9ca3af;">Grup diskusi dan tanya jawab eksklusif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
