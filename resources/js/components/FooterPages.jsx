import React, { useContext, useEffect, useState } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

function LogoSection({ title, items }) {
	return (
		<div className='flex flex-col items-center gap-6 w-full'>
			<h1 className='font-bold text-xl sm:text-2xl tracking-widest text-center uppercase'>
				{title}
			</h1>
			<ul className='flex flex-wrap justify-center gap-x-8 gap-y-10 sm:gap-x-10 md:gap-x-10 lg:gap-x-12'>
				{items.map((item) => (
					<li
						key={item.id}
						className='flex flex-col items-center gap-2 w-28 sm:w-32 md:w-32 lg:w-36'
					>
						<img
							className='h-20 w-28 sm:h-24 sm:w-32 md:h-24 md:w-32 lg:h-28 lg:w-36 object-contain rounded-2xl transition-transform duration-200 hover:scale-110 focus-visible:scale-110'
							src={`/api/images/${item.image}`}
							alt={item.name}
							loading='lazy'
						/>
					</li>
				))}
			</ul>
		</div>
	);
}

export default function FooterPages() {
	const { lang } = useContext(LangContext);
	// { organizers: { visible, items }, partners: { visible, items } } — admins can hide
	// either section from its list page in the dashboard.
	const [sections, setSections] = useState(null);

	useEffect(() => {
		fetch('/api/footer')
			.then((res) => res.json())
			.then(setSections)
			.catch(() => {});
	}, []);

	return (
		<section className='bg-black py-12 px-4'>
			<div className='text-white flex flex-col items-center gap-12 max-w-screen-xl mx-auto'>
				{sections?.organizers?.visible && (
					<LogoSection
						title={lang === 'en' ? 'Organizers' : 'المنظمين'}
						items={sections.organizers.items}
					/>
				)}

				{sections?.partners?.visible && (
					<LogoSection
						title={lang === 'en' ? 'Partners' : 'الشركاء'}
						items={sections.partners.items}
					/>
				)}

				{/* Footer links and info */}
				<div className='flex flex-col items-center gap-4 text-center text-sm sm:text-base w-full'>
					<div className='space-x-2'>
						<a
							href='/'
							className='hover:underline hover:underline-offset-8 text-white hover:text-point-orange transition-colors'
						>
							{lang === 'en'
								? '© Point Iraq Conference'
								: '© مؤتمر بوينت العراق'}
						</a>
						|
						<a
							href='/speakers'
							className='hover:underline hover:underline-offset-8 text-white hover:text-point-orange transition-colors'
						>
							{lang === 'en' ? 'Speakers' : 'المتحدثين'}
						</a>
					</div>

					<div className='text-center leading-relaxed max-w-md sm:max-w-full'>
						{lang === 'en' ? (
							<>
								Tawasoul Organization for Youth Empowerment · Iraq – Baghdad –
								Karradah city · Mobile: +964 770 211 1332
							</>
						) : (
							<>
								منظمة تواصل لتمكين الشباب · العراق – بغداد – مدينة الكرادة ·
								الهاتف: 1332 211 770 967+
							</>
						)}
					</div>
					<div>tawasoul.office@gmail.com</div>

					<a
						href='/'
						className='hover:underline hover:underline-offset-8 text-white hover:text-point-orange transition-colors'
					>
						point-iraq.org
					</a>
				</div>
			</div>
		</section>
	);
}
