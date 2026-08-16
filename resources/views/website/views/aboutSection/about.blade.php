@extends('layouts.guest')

@section('title', 'About - Fitcoin')

@section('content')
<div class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-600/10 border border-indigo-600/30 mb-6">
            <span class="text-sm text-indigo-400">About Us</span>
        </div>

        <h1 class="text-4xl sm:text-5xl font-bold mb-6">
            About <span class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">Fitcoin</span>
        </h1>

        <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-8">
            Fitcoin is your ultimate fitness companion. We help you track workouts, monitor progress, and achieve your health goals.
        </p>

        <div class="grid md:grid-cols-3 gap-6 mt-12">
            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-indigo-600/10 border border-indigo-600/30">
                    <i class="fas fa-bullseye text-2xl text-indigo-400"></i>
                </div>
                <h3 class="font-semibold mb-2">Our Mission</h3>
                <p class="text-gray-400 text-sm">Empower everyone to lead a healthier lifestyle.</p>
            </div>

            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-purple-600/10 border border-purple-600/30">
                    <i class="fas fa-eye text-2xl text-purple-400"></i>
                </div>
                <h3 class="font-semibold mb-2">Our Vision</h3>
                <p class="text-gray-400 text-sm">A world where fitness is accessible to everyone.</p>
            </div>

            <div class="bg-gray-900 rounded-2xl p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-pink-600/10 border border-pink-600/30">
                    <i class="fas fa-heart text-2xl text-pink-400"></i>
                </div>
                <h3 class="font-semibold mb-2">Our Values</h3>
                <p class="text-gray-400 text-sm">Innovation, dedication, and community.</p>
            </div>
        </div>
    </div>
</div>
@endsection