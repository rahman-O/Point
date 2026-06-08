import React, { useContext, useEffect, useState } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function FooterPages() {
	const { lang } = useContext(LangContext);
	const [orgs, setOrgs] = useState([]);
	const [partners, setPartners] = useState([]);

	const fetchOrgsData = async () => {
		const res = await fetch('/api/orgs');
		const data = await res.json();
		setOrgs(data);
	};

	const fetchPartnersData = async () => {
		const res = await fetch('/api/partners');
		const data = await res.json();
		setPartners(data);
	};

	useEffect(() => {
		fetchOrgsData();
		fetchPartnersData();
	}, []);

	return (
		<section className='bg-point-teal py-12 px-4'>
			<div className='text-white flex flex-col items-center gap-12 max-w-screen-xl mx-auto'>
				{/* Organizers */}
				<div className='flex flex-col items-center gap-6 w-full'>
					<h2 className='font-bold text-base sm:text-lg tracking-widest text-center uppercase'>
						{lang === 'en' ? 'Organizers' : 'المنظمين'}
					</h2>
					<ul className='flex flex-wrap justify-center gap-x-6 gap-y-8 sm:gap-x-10'>
						{orgs.map((org) => (
							<li
								key={org.id}
								className='flex flex-col items-center gap-2 w-20 sm:w-24'
							>
								<img
									className='h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 object-contain rounded-2xl transition-transform duration-200 hover:scale-110 focus-visible:scale-110'
									src={`/api/images/${org.image}`}
									alt={org.name}
									loading='lazy'
								/>
								<span className='text-[11px] sm:text-xs text-center leading-tight text-white/90 line-clamp-2'>
									{org.name}
								</span>
							</li>
						))}
					</ul>
				</div>

				{/* Partners */}
				<div className='flex flex-col items-center gap-6 w-full'>
					<h2 className='font-bold text-base sm:text-lg tracking-widest text-center uppercase'>
						{lang === 'en' ? 'Partners' : 'الشركاء'}
					</h2>
					<ul className='flex flex-wrap justify-center gap-x-6 gap-y-8 sm:gap-x-10'>
						{partners.map((partner) => (
							<li
								key={partner.id}
								className='flex flex-col items-center gap-2 w-20 sm:w-24'
							>
								<img
									className='h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 object-contain rounded-2xl transition-transform duration-200 hover:scale-110 focus-visible:scale-110'
									src={`/api/images/${partner.image}`}
									alt={partner.name}
									loading='lazy'
								/>
								<span className='text-[11px] sm:text-xs text-center leading-tight text-white/90 line-clamp-2'>
									{partner.name}
								</span>
							</li>
						))}
					</ul>
				</div>

				{/* Footer links and info */}
				<div className='flex flex-col items-center gap-4 text-center text-sm sm:text-base w-full'>
					<div className='space-x-2'>
						<a
							href='/'
							className='hover:underline hover:underline-offset-8 text-white hover:text-point-rose transition-colors'
						>
							{lang === 'en' ? '© Point Conference' : '© مؤتمر بوينت'}
						</a>
						|
						<a
							href='/speakers'
							className='hover:underline hover:underline-offset-8 text-white hover:text-point-rose transition-colors'
						>
							{lang === 'en' ? 'Speakers' : 'المتحدثين'}
						</a>
						|
						<a
							href='/partners'
							className='hover:underline hover:underline-offset-8 text-white hover:text-point-rose transition-colors'
						>
							{lang === 'en' ? 'Partners' : 'الشركاء'}
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

					<a
						href='/'
						className='hover:underline hover:underline-offset-8 text-white hover:text-point-rose transition-colors'
					>
						point-iraq.org
					</a>
				</div>
			</div>
		</section>
	);
}
