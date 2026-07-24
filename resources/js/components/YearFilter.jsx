import React, { useEffect, useRef, useState } from 'react';

/**
 * Year filter styled as a single connected purple bar. Each year is a segment;
 * the selected year is highlighted in crimson. When there are more years than
 * fit on screen the bar scrolls horizontally (mouse wheel / trackpad / touch),
 * and subtle edge fades hint that there is more to scroll.
 */
export default function YearFilter({ years, selectedYear, onSelect }) {
	const scrollRef = useRef(null);
	const [overflowing, setOverflowing] = useState(false);

	// Detect whether the bar overflows (needs scrolling). A ResizeObserver keeps
	// this accurate on viewport resize and when the year list changes.
	useEffect(() => {
		const el = scrollRef.current;
		if (!el) return;

		const check = () => setOverflowing(el.scrollWidth > el.clientWidth + 4);
		check();

		const observer = new ResizeObserver(check);
		observer.observe(el);
		window.addEventListener('resize', check);
		return () => {
			observer.disconnect();
			window.removeEventListener('resize', check);
		};
	}, [years]);

	// Let a vertical mouse wheel scroll the bar horizontally when it overflows
	// (needs a non-passive listener so we can preventDefault the page scroll).
	useEffect(() => {
		const el = scrollRef.current;
		if (!el) return;

		const handleWheel = (event) => {
			if (el.scrollWidth <= el.clientWidth) return;
			if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
				el.scrollLeft += event.deltaY;
				event.preventDefault();
			}
		};

		el.addEventListener('wheel', handleWheel, { passive: false });
		return () => el.removeEventListener('wheel', handleWheel);
	}, [years]);

	if (!years || years.length === 0) return null;

	return (
		<div className='mb-6 flex justify-start bg-point-purple'>
			<div className='relative inline-flex max-w-full'>
				<div
					ref={scrollRef}
					className='inline-flex max-w-full overflow-x-auto no-scrollbar  bg-point-purple'
				>
					{years.map((year) => {
						const active = String(selectedYear) === String(year);
						return (
							<button
								key={year}
								onClick={() => onSelect(year)}
								className={`flex-none px-4 py-3 text-sm sm:text-base text-white whitespace-nowrap transition-colors ${
									active ? 'bg-point-crimson' : 'hover:bg-point-crimson'
								}`}
							>
								{year}
							</button>
						);
					})}
				</div>

				{overflowing && (
					<>
						<span className='pointer-events-none absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-point-purple to-transparent' />
						<span className='pointer-events-none absolute inset-y-0 right-0 w-8  bg-gradient-to-l from-point-purple to-transparent' />
					</>
				)}
			</div>
		</div>
	);
}
