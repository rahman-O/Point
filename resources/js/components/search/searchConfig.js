import { FiAward, FiCalendar, FiFileText, FiPlayCircle, FiUser } from 'react-icons/fi';

export const SEARCH_TYPES = ['speaker', 'session', 'news', 'stream', 'conference'];

export const MIN_QUERY_LENGTH = 2;

export const TYPE_STYLES = {
	speaker: { icon: FiUser, badge: 'bg-point-purple/10 text-point-purple' },
	session: { icon: FiCalendar, badge: 'bg-point-crimson/10 text-point-crimson' },
	news: { icon: FiFileText, badge: 'bg-point-orange/15 text-[#a85f0a]' },
	stream: { icon: FiPlayCircle, badge: 'bg-red-50 text-red-600' },
	conference: { icon: FiAward, badge: 'bg-point-teal/10 text-point-teal' },
};

const strings = {
	en: {
		placeholder: 'Search speakers, sessions, news, videos…',
		searchLabel: 'Search',
		searchShort: 'Search',
		all: 'All',
		types: { speaker: 'Speakers', session: 'Sessions', news: 'News', stream: 'Videos', conference: 'Conference' },
		typeSingular: { speaker: 'Speaker', session: 'Session', news: 'News', stream: 'Video', conference: 'Conference' },
		searching: 'Searching…',
		noResultsTitle: (q) => `No results for “${q}”`,
		noResultsHint: 'Check the spelling, try fewer words, or search in Arabic.',
		relaxed: 'No result contains all of your words — showing the closest matches.',
		error: 'Something went wrong while searching.',
		retry: 'Try again',
		recent: 'Recent searches',
		clearRecent: 'Clear',
		suggestions: 'Try searching for',
		popular: 'Popular searches',
		quickLinks: 'Browse',
		viewAll: (n) => `View all ${n} results`,
		resultsCount: (n) => `${n} ${n === 1 ? 'result' : 'results'}`,
		resultsFor: (q) => `Results for “${q}”`,
		loadMore: 'Show more results',
		navigate: 'to navigate',
		open: 'to open',
		close: 'to close',
		clear: 'Clear search',
		cancel: 'Cancel',
		day: (d) => `Day ${d}`,
		minChars: 'Type at least 2 characters to search.',
		pageTitle: 'Search',
		pageIntro: 'Search speakers, agenda sessions, news and videos in Arabic or English.',
		opensInNewTab: 'Opens on YouTube',
		suggestionsList: ['Freedom of expression', 'Democracy', 'Journalism', 'Point Iraq'],
	},
	ar: {
		placeholder: 'ابحث عن المتحدثين، الجلسات، الأخبار، الفيديوهات…',
		searchLabel: 'بحث',
		searchShort: 'بحث',
		all: 'الكل',
		types: { speaker: 'المتحدثون', session: 'الجلسات', news: 'الأخبار', stream: 'الفيديوهات', conference: 'المؤتمر' },
		typeSingular: { speaker: 'متحدث', session: 'جلسة', news: 'خبر', stream: 'فيديو', conference: 'المؤتمر' },
		searching: 'جارٍ البحث…',
		noResultsTitle: (q) => `لا توجد نتائج لـ «${q}»`,
		noResultsHint: 'تحقّق من الإملاء، أو جرّب كلمات أقل، أو ابحث باللغة الإنجليزية.',
		relaxed: 'لا توجد نتيجة تحتوي على كل الكلمات — نعرض أقرب النتائج.',
		error: 'حدث خطأ أثناء البحث.',
		retry: 'إعادة المحاولة',
		recent: 'عمليات البحث الأخيرة',
		clearRecent: 'مسح',
		suggestions: 'جرّب البحث عن',
		popular: 'عمليات بحث شائعة',
		quickLinks: 'تصفّح',
		viewAll: (n) => `عرض كل النتائج (${n})`,
		resultsCount: (n) => `${n} نتيجة`,
		resultsFor: (q) => `نتائج البحث عن «${q}»`,
		loadMore: 'عرض المزيد من النتائج',
		navigate: 'للتنقل',
		open: 'للفتح',
		close: 'للإغلاق',
		clear: 'مسح البحث',
		cancel: 'إلغاء',
		day: (d) => `اليوم ${d}`,
		minChars: 'اكتب حرفين على الأقل للبحث.',
		pageTitle: 'البحث',
		pageIntro: 'ابحث في المتحدثين وجلسات جدول الأعمال والأخبار والفيديوهات باللغتين العربية والإنجليزية.',
		opensInNewTab: 'يفتح على يوتيوب',
		suggestionsList: ['حرية التعبير', 'الديمقراطية', 'الصحافة', 'مؤتمر بوينت'],
	},
};

export const searchStrings = (lang) => strings[lang] ?? strings.ar;

export const QUICK_LINKS = [
	{ type: 'speaker', to: '/speakers' },
	{ type: 'session', to: '/programs' },
	{ type: 'news', to: '/news' },
	{ type: 'stream', to: '/stream' },
];

export const isMacPlatform = () =>
	typeof navigator !== 'undefined' && /Mac|iPhone|iPad|iPod/i.test(navigator.platform || navigator.userAgent);

export const formatDate = (date, lang) => {
	try {
		return new Intl.DateTimeFormat(lang === 'en' ? 'en-GB' : 'ar-IQ-u-nu-latn', {
			year: 'numeric',
			month: 'short',
			day: 'numeric',
		}).format(new Date(`${date}T00:00:00`));
	} catch {
		return date;
	}
};
