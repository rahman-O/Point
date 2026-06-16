<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\News;
use App\Models\Organizers;
use App\Models\Partners;
use App\Models\Programs;
use App\Models\SessionsProgram;
use App\Models\Speakers;
use App\Models\Stream;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Seeds realistic demo content for the POINT conference site.
 *
 * This seeder is idempotent: it uses updateOrCreate() with stable keys so it can
 * be run multiple times without creating duplicates and without touching unrelated
 * records that already exist in the database.
 *
 * Image filenames referenced here are real files that already live in
 * storage/app/public, so they resolve through the /api/images/{filename} route.
 */
class DemoContentSeeder extends Seeder
{
    /**
     * A pool of real image filenames that exist in storage/app/public.
     * They are cycled through so every seeded record shows a working image.
     *
     * @var string[]
     */
    private array $images = [
        '01J5KP296H6MH4T95HFY4FF0KZ.png',
        '01J5KVJYMX312N3KE07DWZBYH5.png',
        '01J5KVMB3XNXQFPB8ZZF26G218.png',
        '01J5N3FPGJWSJ1HWEKHFEYTTS4.png',
        '01J5N3J2APAFSK6NSQMNDTZTSZ.png',
        '01J5N49ZVS8RV9CB92Q0TDES6R.png',
        '01J5RNC8GGVK1EW73G7JFR6NP1.png',
        '01J5RNCXK1S82CH9X1V35CCAHJ.png',
        '01J5RNKA4Z0TEADC2TKCH108JA.png',
        '01J5RNQM873N07QGKNFF07DCT2.png',
        '01J5V8K9HXSJQM5MDDRG7WK5CT.png',
        '01J5Y0Y2FHN62DYBTWXD0V1JXW.png',
        '01J5Y0Z25T6X44D0H234C1ET3M.png',
        '01J5Y10Z6XZFP9PC16XVBBVQ8S.jpeg',
        '01J5Y3TW8HMT8C152DVS7N7HTW.jpg',
        '01J5Y4DV5WP4XVSVJY1R3E97XW.png',
        '01J5Y7VGT8WNTZHN1RXAP3NB5F.png',
        '01J5Y87Z0X27PTSET2V2TCPR03.png',
        '01J64VNGRZ7JFDYQW0EHK84W94.png',
        '01J64VPF185NK2PE774GGZ4NWQ.png',
        '01J6829Y2SCN0BYP7VTVBT56Y2.png',
        '01J682CX73RZ74QVS1SJKRXCSK.jpg',
        '01J75TWPDXJD70QQBFSHFVFPQ1.jpg',
        '01J75V8AW1R1262PWJ6SB9P6M5.png',
        '01J76E75R2P5ST98EXEGW7812C.png',
        '01J76E7MKA6AHM6H48MP410N49.png',
        '01J76E81T6EYXNJSYYCM5S6W3N.png',
        '01J76SDS801ZMCHZRA8TV3G5HV.png',
        '01J76SE9D0DEFX891HFWZ9ZXVN.png',
        '01J76SEPX2HTFQR7JAWDNFZ0ZQ.png',
        '01J76SFBMQZHWN70E779A7VZMQ.png',
        '01J76SFVQVKKKJXG4D9F10DN5W.png',
        '01J76SGFW2962PA70CXBAVGB2F.png',
        '01J76SH56NVY7E3PT24F80NK9S.png',
        '01J76XA3SS8FQ82EMSNFK3Y2R2.jpg',
        '01J76XAJG4F468HE6N9MGY10TP.jpg',
        '01J76XB20MX8K1PBF9NE5ET6R2.jpg',
        '01J76XBJRYDF8HF76N1BJZ1RGW.jpg',
        '01J76XC374QE063F4HPJNY56AC.jpg',
        '01J76XCK1GYH3NDTD1QMXSWAV5.jpg',
    ];

    /**
     * Pointer used to cycle through the image pool.
     */
    private int $imageCursor = 0;

    /**
     * Return the next image filename from the pool, cycling back to the start.
     */
    private function nextImage(): string
    {
        $image = $this->images[$this->imageCursor % count($this->images)];
        $this->imageCursor++;

        return $image;
    }

    public function run(): void
    {
        $this->seedSpeakers();
        $this->seedNews();
        $this->seedConference();
        $this->seedStreams();
        $this->seedOrganizers();
        $this->seedPartners();
        $this->seedPrograms();
    }

    /**
     * Create 25 speakers spread across recent conference years.
     */
    private function seedSpeakers(): void
    {
        $firstNames = [
            'Ahmed', 'Layla', 'Omar', 'Sara', 'Yousef', 'Mona', 'Khaled', 'Hana', 'Tariq', 'Nour',
            'Sami', 'Rana', 'Bilal', 'Dina', 'Faris', 'Maya', 'Hadi', 'Lina', 'Ziad', 'Salma',
            'Karim', 'Aya', 'Nabil', 'Reem', 'Jad',
        ];
        $firstNamesAr = [
            'أحمد', 'ليلى', 'عمر', 'سارة', 'يوسف', 'منى', 'خالد', 'هناء', 'طارق', 'نور',
            'سامي', 'رنا', 'بلال', 'دينا', 'فارس', 'مايا', 'هادي', 'لينا', 'زياد', 'سلمى',
            'كريم', 'آية', 'نبيل', 'ريم', 'جاد',
        ];
        $lastNames = [
            'Al-Rashid', 'Haddad', 'Mansour', 'Khalil', 'Saleh', 'Nasser', 'Hassan', 'Darwish', 'Aziz', 'Farouk',
            'Sultan', 'Jaber', 'Kassem', 'Younes', 'Rahman', 'Sabbagh', 'Ibrahim', 'Wahbi', 'Najjar', 'Halabi',
            'Qureshi', 'Barakat', 'Shamoun', 'Tannous', 'Maalouf',
        ];
        $lastNamesAr = [
            'الرشيد', 'حداد', 'منصور', 'خليل', 'صالح', 'ناصر', 'حسن', 'درويش', 'عزيز', 'فاروق',
            'سلطان', 'جابر', 'قاسم', 'يونس', 'رحمن', 'الصباغ', 'إبراهيم', 'وهبي', 'النجار', 'الحلبي',
            'قريشي', 'بركات', 'شمعون', 'طنوس', 'معلوف',
        ];
        $jobsEn = [
            'Software Architect', 'Data Scientist', 'UX Researcher', 'Cloud Engineer', 'AI Specialist',
            'Product Manager', 'Cybersecurity Expert', 'DevOps Lead', 'Mobile Developer', 'Tech Entrepreneur',
        ];
        $jobsAr = [
            'مهندس معماري برمجي', 'عالم بيانات', 'باحث تجربة المستخدم', 'مهندس سحابة', 'أخصائي ذكاء اصطناعي',
            'مدير منتج', 'خبير أمن سيبراني', 'قائد ديف أوبس', 'مطور تطبيقات الهاتف', 'رائد أعمال تقني',
        ];
        $countriesEn = ['Jordan', 'Egypt', 'UAE', 'Saudi Arabia', 'Lebanon', 'Qatar', 'Morocco', 'Tunisia'];
        $countriesAr = ['الأردن', 'مصر', 'الإمارات', 'السعودية', 'لبنان', 'قطر', 'المغرب', 'تونس'];
        $years = ['2024', '2025', '2026'];

        for ($i = 0; $i < 25; $i++) {
            $nameEn = $firstNames[$i] . ' ' . $lastNames[$i];
            $nameAr = $firstNamesAr[$i] . ' ' . $lastNamesAr[$i];

            // The names alternate male / female by index, so match the portrait gender.
            $gender = $i % 2 === 0 ? 'men' : 'women';

            Speakers::updateOrCreate(
                ['name_en' => $nameEn],
                [
                    'name_ar' => $nameAr,
                    'job_en' => $jobsEn[$i % count($jobsEn)],
                    'job_ar' => $jobsAr[$i % count($jobsAr)],
                    'country_en' => $countriesEn[$i % count($countriesEn)],
                    'country_ar' => $countriesAr[$i % count($countriesAr)],
                    'year' => $years[$i % count($years)],
                    'desc_en' => "{$nameEn} is a renowned {$jobsEn[$i % count($jobsEn)]} with over a decade of "
                        . 'experience building products that serve millions of users. A frequent speaker at POINT, '
                        . 'they focus on practical, human-centered technology and mentoring the next generation of engineers.',
                    'desc_ar' => "{$nameAr} هو {$jobsAr[$i % count($jobsAr)]} مرموق يتمتع بخبرة تزيد عن عشر سنوات في "
                        . 'بناء المنتجات التي تخدم ملايين المستخدمين. متحدث دائم في مؤتمر بوينت، ويركز على التقنية '
                        . 'العملية التي تركز على الإنسان وتوجيه الجيل القادم من المهندسين.',
                    'image' => $this->makeSpeakerPhoto($i, $gender),
                ]
            );
        }
    }

    /**
     * Download a real human portrait photo for a speaker and store it in
     * storage/app/public so it resolves through the /api/images/{filename} route.
     *
     * Photos come from randomuser.me (free portrait set). The gender is matched to the
     * speaker's name. If the download fails (e.g. no internet), we fall back to a
     * generated initials avatar so the seeder never breaks and the field is never empty.
     *
     * @param  int  $index     Zero-based speaker index, used to pick a distinct portrait.
     * @param  string  $gender 'men' or 'women'.
     * @return string The stored filename.
     */
    private function makeSpeakerPhoto(int $index, string $gender): string
    {
        // randomuser.me exposes portraits 0-99 per gender; spread picks across the set.
        $portraitId = ($index * 3 + 7) % 100;
        $url = "https://randomuser.me/api/portraits/{$gender}/{$portraitId}.jpg";
        $filename = "speaker-{$gender}-{$portraitId}.jpg";

        try {
            $response = Http::timeout(15)->retry(2, 500)->get($url);

            if ($response->successful() && ! empty($response->body())) {
                Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }
        } catch (\Throwable $e) {
            // Swallow network errors and fall back to a generated avatar below.
        }

        // Fallback: a neutral initials avatar so the profile image is never broken.
        return $this->makeLogoIcon('Speaker ' . ($index + 1), '#0E7490', 'speaker');
    }

    /**
     * Create 25 news articles with event dates spread across recent months.
     */
    private function seedNews(): void
    {
        $titlesEn = [
            'POINT Conference Announces Record Attendance',
            'Keynote Speakers Revealed for This Year',
            'New Innovation Track Added to the Agenda',
            'Early Bird Tickets Now Available',
            'Workshops on AI and Machine Learning Confirmed',
            'Startup Pitch Competition Returns',
            'Networking Sessions Get a Major Upgrade',
            'International Partners Join the Lineup',
            'Hackathon Winners Showcase Their Projects',
            'Sustainability Becomes a Core Theme',
            'Women in Tech Panel Draws Huge Interest',
            'Live Streaming Expands Global Reach',
            'Cloud Computing Sessions Sell Out Fast',
            'Cybersecurity Experts to Lead Deep Dives',
            'Student Scholarship Program Launches',
            'Mobile Development Track Doubles in Size',
            'Open Source Contributors Honored on Stage',
            'Design Thinking Masterclass Announced',
            'Data Science Bootcamp Joins the Program',
            'Robotics Demos Take Center Stage',
            'Fireside Chat with Industry Leaders Scheduled',
            'Accessibility in Tech Gets Spotlight',
            'New Venue Promises a Bigger Experience',
            'Volunteer Applications Are Open',
            'Closing Ceremony to Feature Award Highlights',
        ];

        $titlesAr = [
            'مؤتمر بوينت يعلن عن حضور قياسي',
            'الكشف عن المتحدثين الرئيسيين لهذا العام',
            'إضافة مسار ابتكار جديد إلى جدول الأعمال',
            'تذاكر الحجز المبكر متاحة الآن',
            'تأكيد ورش عمل حول الذكاء الاصطناعي وتعلم الآلة',
            'عودة مسابقة عروض الشركات الناشئة',
            'تطوير كبير لجلسات التواصل',
            'انضمام شركاء دوليين إلى القائمة',
            'الفائزون في الهاكاثون يعرضون مشاريعهم',
            'الاستدامة تصبح محورًا أساسيًا',
            'جلسة المرأة في التقنية تجذب اهتمامًا كبيرًا',
            'البث المباشر يوسع الوصول العالمي',
            'نفاد جلسات الحوسبة السحابية بسرعة',
            'خبراء الأمن السيبراني يقودون جلسات معمقة',
            'إطلاق برنامج المنح الدراسية للطلاب',
            'مسار تطوير الهاتف المحمول يتضاعف حجمه',
            'تكريم مساهمي المصادر المفتوحة على المسرح',
            'الإعلان عن ورشة احترافية في التفكير التصميمي',
            'معسكر علم البيانات ينضم إلى البرنامج',
            'عروض الروبوتات تتصدر المشهد',
            'جدولة حوار جانبي مع قادة الصناعة',
            'تسليط الضوء على إمكانية الوصول في التقنية',
            'مكان جديد يعد بتجربة أكبر',
            'فتح باب التقديم للمتطوعين',
            'حفل الختام يعرض أبرز الجوائز',
        ];

        $authorsEn = ['Editorial Team', 'Press Office', 'Media Desk', 'POINT Newsroom', 'Communications'];
        $authorsAr = ['فريق التحرير', 'المكتب الصحفي', 'قسم الإعلام', 'غرفة أخبار بوينت', 'قسم الاتصالات'];

        for ($i = 0; $i < 25; $i++) {
            $eventTime = Carbon::create(2026, 1, 1)->addDays($i * 9);

            News::updateOrCreate(
                ['title_en' => $titlesEn[$i]],
                [
                    'title_ar' => $titlesAr[$i],
                    'author_en' => $authorsEn[$i % count($authorsEn)],
                    'author_ar' => $authorsAr[$i % count($authorsAr)],
                    'desc_en' => $titlesEn[$i] . '. ' . 'The POINT organizing committee shared further details today, '
                        . 'highlighting how this update enriches the experience for attendees, speakers, and partners alike. '
                        . 'More announcements will follow in the coming weeks as the program takes its final shape.',
                    'desc_ar' => $titlesAr[$i] . '. ' . 'شاركت لجنة تنظيم بوينت مزيدًا من التفاصيل اليوم، '
                        . 'مسلطة الضوء على كيفية إثراء هذا التحديث لتجربة الحضور والمتحدثين والشركاء على حد سواء. '
                        . 'ستتبع المزيد من الإعلانات في الأسابيع القادمة مع اكتمال البرنامج.',
                    'image' => $this->nextImage(),
                    'event_time' => $eventTime->toDateString(),
                ]
            );
        }
    }

    /**
     * Create the main conference record (used by the conference page).
     */
    private function seedConference(): void
    {
        Conference::updateOrCreate(
            ['title_en' => 'POINT Conference 2026'],
            [
                'title_ar' => 'مؤتمر بوينت 2026',
                'desc_en' => 'POINT is the region\'s flagship technology conference, bringing together developers, '
                    . 'designers, entrepreneurs, and industry leaders for five days of talks, workshops, and networking. '
                    . 'This year we focus on artificial intelligence, cloud-native engineering, and building products that '
                    . 'matter. Join thousands of attendees from across the globe for an unforgettable experience filled '
                    . 'with learning, collaboration, and inspiration.',
                'desc_ar' => 'بوينت هو المؤتمر التقني الرائد في المنطقة، حيث يجمع المطورين والمصممين ورواد الأعمال '
                    . 'وقادة الصناعة لمدة خمسة أيام من المحادثات وورش العمل والتواصل. نركز هذا العام على الذكاء الاصطناعي '
                    . 'والهندسة السحابية وبناء منتجات ذات قيمة. انضم إلى آلاف الحضور من جميع أنحاء العالم لتجربة لا تُنسى '
                    . 'مليئة بالتعلم والتعاون والإلهام.',
                'image' => $this->nextImage(),
            ]
        );
    }

    /**
     * Create 25 video streams (idempotent on the YouTube URL).
     */
    private function seedStreams(): void
    {
        $videoIds = [
            'dQw4w9WgXcQ', '9bZkp7q19f0', 'kJQP7kiw5Fk', 'fJ9rUzIMcZQ', 'OPf0YbXqDm0',
            'e-ORhEE9VVg', '3JZ_D3ELwOQ', 'CevxZvSJLk8', 'JGwWNGJdvx8', 'RgKAFK5djSk',
            'hLQl3WQQoQ0', 'pRpeEdMmmQ0', 'YQHsXMglC9A', 'tVj0ZTS4WF4', 'lp-EO5I60KA',
            'kffacxfA7G4', 'uelHwf8o7_U', 'MtN1YnoL46Q', 'WPni755-Krg', '2Vv-BfVoq4g',
            '1G4isv_Fylg', 'QcIy9NiNbmo', 'PT2_F-1esPk', 'AdUw5RdyZxI', 'nfWlot6h_JM',
        ];

        for ($i = 0; $i < 25; $i++) {
            $url = 'https://www.youtube.com/watch?v=' . $videoIds[$i];
            $date = Carbon::create(2026, 1, 1)->addDays($i * 7);

            Stream::updateOrCreate(
                ['url' => $url],
                [
                    'title_en' => 'POINT Session Recording #' . ($i + 1),
                    'title_ar' => 'تسجيل جلسة بوينت رقم ' . ($i + 1),
                    'date' => $date->toDateString(),
                ]
            );
        }
    }

    /**
     * Create a set of event organizers (shown in the footer as logo icons).
     *
     * If you drop real logo files into database/seeders/assets/organizers, those are
     * used. Otherwise it falls back to generated initials badges so the seeder always
     * produces something.
     */
    private function seedOrganizers(): void
    {
        if ($this->seedLogosFromFolder('organizers', Organizers::class, 'org')) {
            return;
        }

        $organizers = [
            ['POINT Foundation', '#0E7490'],
            ['Tech Community Hub', '#7C3AED'],
            ['Digital Innovation Council', '#DB2777'],
            ['Future Builders Network', '#EA580C'],
            ['Code for the Region', '#059669'],
            ['Open Source Alliance', '#2563EB'],
        ];

        foreach ($organizers as [$name, $color]) {
            Organizers::updateOrCreate(
                ['name' => $name],
                ['image' => $this->makeLogoIcon($name, $color, 'org')]
            );
        }
    }

    /**
     * Create a set of partners / sponsors (shown in the footer as logo icons).
     *
     * If you drop real logo files into database/seeders/assets/partners, those are
     * used. Otherwise it falls back to generated initials badges so the seeder always
     * produces something.
     */
    private function seedPartners(): void
    {
        if ($this->seedLogosFromFolder('partners', Partners::class, 'partner')) {
            return;
        }

        $partners = [
            ['CloudScale Inc.', '#2563EB'],
            ['DataForge Labs', '#0891B2'],
            ['NextGen Devices', '#9333EA'],
            ['SecureNet Systems', '#DC2626'],
            ['BrightUI Studio', '#D97706'],
            ['Quantum Hosting', '#4F46E5'],
            ['GreenTech Ventures', '#16A34A'],
            ['PixelWorks Media', '#DB2777'],
        ];

        foreach ($partners as [$name, $color]) {
            Partners::updateOrCreate(
                ['name' => $name],
                ['image' => $this->makeLogoIcon($name, $color, 'partner')]
            );
        }
    }

    /**
     * Import real logo files that the user has dropped into
     * database/seeders/assets/{$folder} (e.g. organizers / partners).
     *
     * Each image is copied into storage/app/public (so it resolves through the
     * /api/images/{filename} route) and one record is created per file. The display
     * name comes from the file name, so name your files like "POINT Foundation.png".
     *
     * Supported types: png, jpg, jpeg, svg, webp, gif.
     *
     * @param  string  $folder  Sub-folder under database/seeders/assets.
     * @param  class-string  $model  Eloquent model to populate (Organizers / Partners).
     * @param  string  $prefix  Prefix for the stored filename to avoid collisions.
     * @return bool  True if at least one logo file was imported; false if the folder is
     *               empty or missing (so the caller can fall back to generated icons).
     */
    private function seedLogosFromFolder(string $folder, string $model, string $prefix): bool
    {
        $sourceDir = database_path('seeders/assets/' . $folder);

        if (! is_dir($sourceDir)) {
            return false;
        }

        $files = glob($sourceDir . '/*.{png,jpg,jpeg,svg,webp,gif,PNG,JPG,JPEG,SVG,WEBP,GIF}', GLOB_BRACE);
        $files = array_values(array_filter((array) $files, 'is_file'));

        if (empty($files)) {
            return false;
        }

        $keepNames = [];

        foreach ($files as $path) {
            $base = pathinfo($path, PATHINFO_FILENAME);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            // Display name: turn "point_foundation" / "point-foundation" into
            // "point foundation" (keep the user's casing otherwise).
            $name = trim(preg_replace('/[_-]+/', ' ', $base));
            $keepNames[] = $name;

            // Stable stored filename so re-running the seeder overwrites the same file
            // instead of creating duplicates.
            $stored = $prefix . '-' . Str::slug($base) . '.' . $ext;

            Storage::disk('public')->put($stored, file_get_contents($path));

            $model::updateOrCreate(
                ['name' => $name],
                ['image' => $stored]
            );
        }

        // Remove any previously seeded records (e.g. the generated placeholder logos
        // or files you have since deleted) so only the logos in this folder remain.
        $model::whereNotIn('name', $keepNames)->delete();

        return true;
    }

    /**
     * Generate a simple company logo icon (SVG) for the given name and save it to
     * storage/app/public so it resolves through the /api/images/{filename} route.
     *
     * The icon is a compact rounded badge in the brand colour showing the company
     * initials only - a crisp, scalable logo mark (not a photo) that stays sharp at
     * small footer sizes. The full name is surfaced as an HTML caption / alt text.
     *
     * @return string The stored filename.
     */
    private function makeLogoIcon(string $name, string $color, string $prefix): string
    {
        // Build up to two initials from the company name (ignoring punctuation/suffixes).
        $words = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9 ]/', '', $name));
        $words = array_values(array_filter($words));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper($word[0]);
        }
        $initials = $initials !== '' ? $initials : 'CO';

        // A clean, scalable logo mark: a rounded badge in the brand colour with the
        // company initials only. The company name is intentionally NOT baked into the
        // SVG so the mark stays crisp at small footer sizes; the name is rendered as an
        // accessible HTML caption / alt text in the UI instead.
        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120" role="img" aria-label="{$initials} logo">
          <rect width="120" height="120" rx="24" fill="{$color}"/>
          <text x="60" y="60" font-family="Helvetica, Arial, sans-serif" font-size="44" font-weight="700" fill="#FFFFFF" text-anchor="middle" dominant-baseline="central">{$initials}</text>
        </svg>
        SVG;

        $filename = $prefix . '-' . Str::slug($name) . '.svg';
        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }

    /**
     * Create 2 full programs (agendas) with days, sessions, speakers and facilitators.
     */
    private function seedPrograms(): void
    {
        // Pull a pool of speaker ids to attach to sessions.
        $speakerIds = Speakers::orderBy('id')->pluck('id')->all();

        // Program 1 — the current-year program (homepage reads /programs/current/year => 2026).
        $program2026 = Programs::updateOrCreate(
            ['year' => '2026'],
            ['day1' => true, 'day2' => true, 'day3' => true, 'day4' => false, 'day5' => false]
        );

        // Program 2 — last year's archived program.
        $program2025 = Programs::updateOrCreate(
            ['year' => '2025'],
            ['day1' => true, 'day2' => true, 'day3' => false, 'day4' => false, 'day5' => false]
        );

        $this->seedSessionsForProgram($program2026, ['day1', 'day2', 'day3'], $speakerIds);
        $this->seedSessionsForProgram($program2025, ['day1', 'day2'], $speakerIds);
    }

    /**
     * Build a full daily agenda for a program. Existing sessions for the program are
     * cleared first so repeated runs stay clean and never duplicate rows.
     *
     * @param  string[]  $days
     * @param  int[]  $speakerIds
     */
    private function seedSessionsForProgram(Programs $program, array $days, array $speakerIds): void
    {
        // Clear only this program's sessions (cascade removes pivot rows safely).
        SessionsProgram::where('programs_id', $program->id)->get()->each(function ($session) {
            $session->speakers()->detach();
            $session->delete();
        });

        // A repeatable agenda template used for every day of the program.
        $slots = [
            ['09:00', '09:45', 'Opening Keynote', 'الكلمة الذكاء الاصطناعي  الافتتاحية', 'The State of Technology', 'حالة التقنية'],
            ['10:00', '10:45', 'Building Building for Scale Building for Scale Building for Scale Building for Scale Building for Scale. Building for Scale Building for Scale for Scale', ' البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء للتوسع البناء الذكاء الاصطناعي للتوسع', 'Cloud-Native Architecture', 'الهندسة السحابية'],
            ['11:00', '11:45', 'AI in Practice', 'الذكاء الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الاصطناعي عمليًا', 'Applied Machine Learning', 'تعلم الآلة التطبيقي'],
            ['13:00', '13:45', 'Designing Delight', 'تصميم الذكاء الاصطناعي الذكاء الاصطناعي  الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي البهجة', 'Human-Centered UX', 'تجربة المستخدم المتمحورة حول الإنسان'],
            ['14:00', '14:45', 'Securing Systems', 'تأمين  الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الأنظمة', 'Modern Cybersecurity', 'الأمن السيبراني الحديث'],
            ['15:00', '15:45', 'Panel & Closing', 'الجلسة الحوارية الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي الذكاء الاصطناعي والختام', 'Future of the Industry', 'مستقبل الصناعة'],
        ];

        $speakerCount = count($speakerIds);
        $rotation = 0;

        foreach ($days as $day) {
            foreach ($slots as $slot) {
                [$start, $end, $titleEn, $titleAr, $subjectEn, $subjectAr] = $slot;

                // Pick 1-3 speakers for this session, rotating through the pool.
                $sessionSpeakerIds = [];
                if ($speakerCount > 0) {
                    $howMany = ($rotation % 3) + 1; // 1, 2 or 3 speakers
                    for ($k = 0; $k < $howMany; $k++) {
                        $sessionSpeakerIds[] = $speakerIds[($rotation + $k) % $speakerCount];
                    }
                }

                $facilitatorId = $sessionSpeakerIds[0] ?? null;

                $session = SessionsProgram::create([
                    'programs_id' => $program->id,
                    'day' => $day,
                    'year' => $program->year,
                    'start_time' => $start,
                    'end_time' => $end,
                    'subject_en' => $subjectEn,
                    'subject_ar' => $subjectAr,
                    'presentation_en' => $subjectEn . ' — an in-depth talk with live examples and Q&A.',
                    'presentation_ar' => $subjectAr . ' — جلسة معمقة مع أمثلة حية وأسئلة وأجوبة.',
                    'title_en' => $titleEn,
                    'title_ar' => $titleAr,
                    'facilitator_id' => $facilitatorId,
                ]);

                if (! empty($sessionSpeakerIds)) {
                    $session->speakers()->sync($sessionSpeakerIds);
                }

                $rotation++;
            }
        }
    }
}
