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

	// Optional calendar date set by admins ("YYYY-MM-DD"), shown as a small second line
	// under the day label, e.g. "Tue 24 Nov" / "الثلاثاء، 24 تشرين الثاني". Arabic names
	// are spelled out here (with the definite article) because browsers differ in how
	// they abbreviate Arabic weekdays.
	const AR_WEEKDAYS = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
	const AR_MONTHS = [
		'كانون الثاني', 'شباط', 'آذار', 'نيسان', 'أيار', 'حزيران',
		'تموز', 'آب', 'أيلول', 'تشرين الأول', 'تشرين الثاني', 'كانون الأول',
	];
	const dateLabel = (key) => {
		const iso = program?.day_dates?.[key];
		if (!iso) return null;
		const [y, m, d] = iso.split('-').map(Number);
		const date = new Date(y, m - 1, d);
		if (lang === 'ar') {
			return `${AR_WEEKDAYS[date.getDay()]}، ${d} ${AR_MONTHS[m - 1]}`;
		}
		return new Intl.DateTimeFormat('en-GB', {
			weekday: 'short',
			day: 'numeric',
			month: 'short',
		}).format(date);
	};

	// Find out which days have at least one session in `sessions_program`
	const availableDays = days.filter((day) => {
		return (
			program &&
			program.sessions_program?.some((session) => session.day === day.key)
		);
	});

	return (
		<div className='flex flex-row w-full mt-12 justify-center  flex-wrap item-center text-center'>
			{availableDays.length > 0 ? (
				availableDays.map((day, index) => {
					const date = dateLabel(day.key);

					return (
						<div key={index} className='gap-4 mx-3'>
							<Tabs
								aria-label={day.key}
								variant={activeTab === day.key ? 'pills' : 'default'}
								onActiveTabChange={() => setActiveTab(day.key)}
							>
								<Tabs.Item
									title={
										<span className='flex flex-col items-center leading-tight'>
											<span>{lang === 'ar' ? day.ar : day.en}</span>
											{date && (
												<span className='mt-0.5 text-[11px] font-normal opacity-70'>
													{date}
												</span>
											)}
										</span>
									}
								/>
							</Tabs>
						</div>
					);
				})
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
