import React, { useContext } from 'react';
import { Tabs } from 'flowbite-react';
import LangContext from '@/components/langContext/LangContext.jsx';

export function TapsSessions({ activeTab, setActiveTab, program }) {
	const { lang } = useContext(LangContext);

	// Days array with English and Arabic labels
	const days = [
		{ key: 'day1', en: 'First Day', ar: 'اليوم الأول' },
		{ key: 'day2', en: 'Second Day', ar: 'اليوم الثاني' },
		{ key: 'day3', en: 'Third Day', ar: 'اليوم الثالث' },
		{ key: 'day4', en: 'Fourth Day', ar: 'اليوم الرابع' },
		{ key: 'day5', en: 'Fifth Day', ar: 'اليوم الخامس' },
	];

	// Find out which days have at least one session in `sessions_program`
	const availableDays = days.filter((day) => {
		return (
			program &&
			program.sessions_program?.some((session) => session.day === day.key)
		);
	});

	return (
		<div className='flex flex-row w-full mt-[60] justify-center'>
			{availableDays.length > 0 ? (
				availableDays.map((day, index) => (
					<div key={index} className='gap-4 mr-3'>
						<Tabs
							aria-label={day.key}
							variant={activeTab === day.key ? 'pills' : 'default'}
							onActiveTabChange={() => setActiveTab(day.key)}
						>
							<Tabs.Item title={lang === 'ar' ? day.ar : day.en} />
						</Tabs>
					</div>
				))
			) : (
				<p>
					{lang === 'en'
						? 'No days available with sessions'
						: 'لا توجد ايام محددة لهذه الجلسة'}{' '}
				</p>
			)}
		</div>
	);
}
