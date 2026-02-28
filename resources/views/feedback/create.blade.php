<x-layouts.app :title="'Bantuan & Feedback'">
    <div class="max-w-2xl mx-auto py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Bantuan & Feedback</h1>
            <p class="text-slate-500 mt-2">Apakah Anda menemukan kendala atau memiliki saran fitur? Beritahu kami!</p>
        </div>

        <div class="card shadow-sm border border-slate-200 p-6 bg-white rounded-2xl">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label for="subject" style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Subjek / Topik</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required 
                        placeholder="Contoh: Bug di halaman dashboard" 
                        style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; background: #f8fafc; transition: border-color 0.2s;">
                    @error('subject') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="message" style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Pesan Anda</label>
                    <textarea name="message" id="message" rows="5" required 
                        placeholder="Jelaskan secara detail kendala atau saran Anda..." 
                        style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; background: #f8fafc; transition: border-color 0.2s; resize: vertical;">{{ old('message') }}</textarea>
                    @error('message') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary" style="background: #0f172a; color: white; padding: 10px 24px; border-radius: 10px; font-weight: 600; cursor: pointer;">
                        Kirim Feedback
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 p-4 bg-slate-50 border border-slate-200 rounded-xl">
            <h4 style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 4px;">Catatan:</h4>
            <p style="font-size: 13px; color: #64748b; line-height: 1.5;">Tim admin kami akan meninjau pesan Anda secepatnya. Status bantuan Anda dapat dipantau (segera) melalui halaman profil atau notifikasi jika ada pembaruan.</p>
        </div>
    </div>
</x-layouts.app>
