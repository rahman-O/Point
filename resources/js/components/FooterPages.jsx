import React, { useContext, useEffect, useState } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

// Equal-area sizing: every logo gets about the same visual weight, so wide logos become wider
// and shorter and square ones more compact (within height and width limits).
const LOGO_AREA = 6800;
const LOGO_MIN_HEIGHT = 30;
const LOGO_MAX_HEIGHT = 84;
const LOGO_MAX_WIDTH = 200;

function logoSize({ width, height }) {
	const ratio = width / height;
	let h = Math.min(
		LOGO_MAX_HEIGHT,
		Math.max(LOGO_MIN_HEIGHT, Math.sqrt(LOGO_AREA / ratio)),
	);
	let w = h * ratio;
	if (w > LOGO_MAX_WIDTH) {
		w = LOGO_MAX_WIDTH;
		h = w / ratio;
	}
	return { width: Math.round(w), height: Math.round(h) };
}

function Logo({ item }) {
	// The server makes a white copy of each logo; the original is shown if it couldn't.
	if (!item.logo) {
		return (
			<img
				className='h-12 w-24 sm:h-14 sm:w-28 object-contain'
				src={`/api/images/${item.image}`}
				alt={item.name}
				loading='lazy'
			/>
		);
	}

	const { width, height } = logoSize(item.logo);

	return (
		<img
			className='h-[calc(var(--logo-h)*0.75)] w-[calc(var(--logo-w)*0.75)] sm:h-[var(--logo-h)] sm:w-[var(--logo-w)] object-contain opacity-90 transition duration-200 hover:opacity-100 hover:scale-105'
			style={{ '--logo-w': `${width}px`, '--logo-h': `${height}px` }}
			src={item.logo.src}
			width={width}
			height={height}
			alt={item.name}
			loading='lazy'
		/>
	);
}

function LogoSection({ title, items }) {
	return (
		<div className='flex flex-col items-center gap-8 w-full'>
			<h1 className='font-bold text-xl sm:text-2xl tracking-widest text-center uppercase'>
				{title}
			</h1>
			<ul className='flex flex-wrap items-center justify-center max-w-5xl gap-x-10 gap-y-8 sm:gap-x-14 sm:gap-y-10'>
				{items.map((item) => (
					<li key={item.id} className='flex items-center justify-center'>
						<Logo item={item} />
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
