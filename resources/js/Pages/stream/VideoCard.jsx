import React, { useState } from 'react';

// A full YouTube embed costs ~1 MB of player code and a dozen requests, so a page of ten
// made the browser spin for seconds. Until the visitor presses play, each video is just
// its poster image (~17 KB) with a play button; the real player is created on click only.

// Warm up the player's origins when the visitor is about to click, so the player appears
// faster once they do. Done once per page.
let preconnected = false;
function preconnect() {
	if (preconnected) return;
	preconnected = true;
	for (const href of ['https://www.youtube-nocookie.com', 'https://www.google.com']) {
		const link = document.createElement('link');
		link.rel = 'preconnect';
		link.href = href;
		document.head.appendChild(link);
	}
}

export default function VideoCard({ videoId, title, playing, onPlay, lang }) {
	const [posterFailed, setPosterFailed] = useState(false);

	if (playing) {
		return (
			<iframe
				src={`https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`}
				allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
				allowFullScreen
				title={title}
				className='h-56 w-full rounded-lg'
			/>
		);
	}

	return (
		<button
			type='button'
			onClick={onPlay}
			onPointerEnter={preconnect}
			onFocus={preconnect}
			aria-label={`${lang === 'en' ? 'Play' : 'تشغيل'}: ${title}`}
			className='group relative block h-56 w-full overflow-hidden rounded-lg bg-black text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/60'
		>
			{!posterFailed && (
				// hqdefault always exists (unlike maxresdefault) and is 4:3 with letterbox bars;
				// object-cover trims the bars so it fills the 16:9 card like the player did.
				<img
					src={`https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`}
					alt=''
					loading='lazy'
					decoding='async'
					onError={() => setPosterFailed(true)}
					className='h-full w-full object-cover transition-transform duration-300 group-hover:scale-105'
				/>
			)}

			{/* Title over a gradient at the top, as the YouTube player itself shows it. */}
			<span className='pointer-events-none absolute inset-x-0 top-0 bg-gradient-to-b from-black/70 to-transparent px-3 pb-6 pt-2.5 text-sm font-semibold leading-snug text-white line-clamp-2'>
				{title}
			</span>

			<span className='pointer-events-none absolute inset-0 flex items-center justify-center'>
				<span className='flex h-12 w-[68px] items-center justify-center rounded-[14px] bg-[#212121]/80 transition-colors group-hover:bg-[#f00]'>
					<svg viewBox='0 0 24 24' aria-hidden='true' className='h-6 w-6 fill-white'>
						<path d='M8 5v14l11-7z' />
					</svg>
				</span>
			</span>
		</button>
	);
}
