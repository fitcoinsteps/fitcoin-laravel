<footer class="relative z-20 w-full border-t border-white/10 bg-[#08030f] pt-14 pb-8">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 text-gray-400 text-sm mb-12 border-b border-white/5 pb-12">
            <div class="space-y-4 col-span-1 md:col-span-1">
                <h4 class="text-white font-bold text-2xl bg-clip-text text-transparent bg-gradient-to-r from-neonpink to-neonpurple">Fitcoin</h4>
                <p class="leading-relaxed text-gray-500 text-[15px]">Turn steps into crypto. Join the fitness revolution today.</p>
                <div class="flex flex-wrap gap-4 mt-3 text-white">
                    <a href="#" class="hover:text-neonpink transition transform hover:scale-125 duration-200"><i class="fab fa-facebook-f text-xl"></i></a>
                    <a href="#" class="hover:text-neonpink transition transform hover:scale-125 duration-200"><i class="fab fa-instagram text-xl"></i></a>
                    <a href="#" class="hover:text-neonpink transition transform hover:scale-125 duration-200"><i class="fab fa-x-twitter text-xl"></i></a>
                    <a href="#" class="hover:text-neonpink transition transform hover:scale-125 duration-200"><i class="fab fa-telegram text-xl"></i></a>
                    <a href="#" class="hover:text-neonpink transition transform hover:scale-125 duration-200"><i class="fab fa-discord text-xl"></i></a>
                    <a href="#" class="hover:text-neonpink transition transform hover:scale-125 duration-200"><i class="fab fa-tiktok text-xl"></i></a>
                </div>
            </div>
            <div class="space-y-3">
                <h4 class="text-white font-semibold">Earn & Rewards</h4>
                <ul class="space-y-2">
                    <li><a href="{{ url('/pages/ways-to-earn') }}" class="hover:text-white transition">Ways to Earn</a></li>
                    <li><a href="{{ url('/pages/referral') }}" class="hover:text-white transition">Referral Program</a></li>
                    <li><a href="{{ url('/pages/tiers') }}" class="hover:text-white transition">Tiers & Levels</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-white font-semibold">Support & Contact</h4>
                <ul class="space-y-2">
                    <li><a href="{{ url('/pages/faq') }}" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="mailto:contact@fit-coin.net" class="hover:text-white transition">contact@fit-coin.net</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-white font-semibold">Legal & Account</h4>
                <ul class="space-y-2">
                    <li><a href="{{ url('/pages/privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="{{ url('/pages/terms') }}" class="hover:text-white transition">Terms of Use</a></li>
                    <li><a href="{{ url('/pages/delete-account') }}" class="text-red-400 hover:text-red-300 transition">Delete Account</a></li>
                </ul>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-500">
            <span>&copy; {{ date('Y') }} Fitcoin. All rights reserved.</span>
            <div class="flex gap-4 mt-4 md:mt-0">
                <a href="{{ url('/pages/privacy') }}" class="hover:text-gray-300">Privacy</a>
                <a href="{{ url('/pages/terms') }}" class="hover:text-gray-300">Terms</a>
            </div>
        </div>
    </div>
</footer>