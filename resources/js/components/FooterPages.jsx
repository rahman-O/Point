import React, { useContext, useEffect, useState } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

// Logos sit in a grid of equal cells so the rows line up. Inside a cell each logo is sized
// for equal *visual weight* rather than equal height: the eye judges a logo by the area it
// covers, so a square badge needs to be much taller than a wide wordmark to look the same
// size. The server tells us each logo's shape and how much of its box is actually painted;
// both feed the size, so new partners get the same treatment automatically.
const CELL_WIDTH = 200;
const CELL_HEIGHT = 96;
// Height of a 1:1 logo of average ink density; wider logos get less, clamped to the cell.
const SQUARE_LOGO_HEIGHT = 84;
const LOGO_MAX_HEIGHT = 90;
// 0 would give every logo the same height, 0.5 the same area; a bit under 0.5 keeps wide
// wordmarks legible without letting square marks dominate.
const SHAPE_EXPONENT = 0.42;
// Logos painted more sparsely than this (thin line art) are enlarged a little, denser ones
// (solid badges) are reduced a little, by at most ±15%.
const INK_REFERENCE = 0.32;
const INK_ADJUST_LIMIT = 0.15;

function logoSize({ width, height, ink }) {
	const ratio = width / height;
	const inkAdjust = Math.min(
		1 + INK_ADJUST_LIMIT,
		Math.max(1 - INK_ADJUST_LIMIT, Math.pow(INK_REFERENCE / (ink || INK_REFERENCE), 0.25)),
	);
	let h = Math.min(LOGO_MAX_HEIGHT, SQUARE_LOGO_HEIGHT * Math.pow(ratio, -SHAPE_EXPONENT) * inkAdjust);
	let w = h * ratio;
	if (w > CELL_WIDTH) {
		w = CELL_WIDTH;
		h = w / ratio;
	}
	return { width: Math.round(w), height: Math.round(h) };
}

function Logo({ item }) {
	// The server makes a white copy of each logo; the original is shown if it couldn't.
	if (!item.logo) {
		return (
			<img
				className='max-h-full max-w-full object-contain'
				src={`/api/images/${item.image}`}
				alt={item.name}
				loading='lazy'
			/>
		);
	}

	const { width, height } = logoSize(item.logo);

	return (
		<img
			// Same opacity for every logo evens out solid, heavy marks against thin, line-art ones.
			className='h-[calc(var(--logo-h)*0.7)] w-[calc(var(--logo-w)*0.7)] sm:h-[var(--logo-h)] sm:w-[var(--logo-w)] object-contain opacity-85 transition duration-200 hover:opacity-100 hover:scale-105'
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
		<div className='flex flex-col items-center gap-10 w-full'>
			{/* A short brand-coloured rule under the title gives the single word an anchor over
			    the logo grid without inflating the type. */}
			<div className='flex flex-col items-center gap-3'>
				<h1 className='font-bold text-2xl sm:text-4xl tracking-widest text-center uppercase'>
					{title}
				</h1>
				<span aria-hidden='true' className='block h-1 w-12 rounded-full bg-point-orange' />
			</div>
			{/* Fixed-size cells in a centred grid: columns line up and the last row centres itself.
			    The number of cells per row follows the viewport, so any number of partners fits;
			    phones get smaller cells so at least two sit side by side. */}
			<ul
				className='grid w-full max-w-6xl justify-center gap-x-3 gap-y-8 sm:gap-x-6 sm:gap-y-12 [grid-template-columns:repeat(auto-fit,minmax(var(--cell-w-sm),var(--cell-w-sm)))] sm:[grid-template-columns:repeat(auto-fit,minmax(var(--cell-w),var(--cell-w)))]'
				style={{ '--cell-w': `${CELL_WIDTH}px`, '--cell-w-sm': `${Math.round(CELL_WIDTH * 0.7)}px` }}
			>
				{items.map((item) => (
					<li
						key={item.id}
						className='flex items-center justify-center h-[calc(var(--cell-h)*0.75)] sm:h-[var(--cell-h)]'
						style={{ '--cell-h': `${CELL_HEIGHT}px` }}
					>
						<Logo item={item} />
					</li>
				))}
			</ul>
		</div>
	);
}

// One number for both languages, isolated as left-to-right so it reads correctly inside
// Arabic text without being typed backwards.
const PHONE = '+964 770 211 1332';

function PhoneNumber() {
	return (
		<a
			href={`tel:${PHONE.replace(/\s+/g, '')}`}
			dir='ltr'
			className='hover:underline hover:underline-offset-8 hover:text-point-orange transition-colors'
		>
			{PHONE}
		</a>
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
		<section className='bg-black pt-16 pb-12 px-4 sm:pt-20'>
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
								Karradah city · Mobile: <PhoneNumber />
							</>
						) : (
							<>
								منظمة تواصل لتمكين الشباب · العراق – بغداد – مدينة الكرادة ·
								الهاتف: <PhoneNumber />
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
