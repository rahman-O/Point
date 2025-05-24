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
		<section className='bg-black py-12 px-4'>
			<div className='text-white flex flex-col items-center gap-12 max-w-screen-xl mx-auto'>
				{/* Organizers */}
				<div className='flex flex-col items-center gap-6 w-full'>
					<h1 className='font-bold text-lg  tracking-widest text-center'>
						Organizers
					</h1>
					<div className='flex flex-wrap justify-center gap-4'>
						{orgs.map((org) => (
							<div key={org.id} className='flex justify-center items-center'>
								<img
									className='h-10 sm:h-14 md:h-16 object-contain'
									src={`/api/images/${org.image}`}
									alt={org.name}
								/>
							</div>
						))}
					</div>
				</div>

				{/* Partners */}
				<div className='flex flex-col items-center gap-6 w-full'>
					<h1 className='font-bold text-lg  tracking-widest text-center'>
						Partners
					</h1>
					<div className='flex flex-wrap justify-center gap-6'>
						{partners.map((partner) => (
							<div
								key={partner.id}
								className='flex justify-center items-center'
							>
								<img
									className='h-16 sm:h-20 md:h-24 object-contain'
									src={`/api/images/${partner.image}`}
									alt={partner.name}
								/>
							</div>
						))}
					</div>
				</div>

				{/* Footer links and info */}
				<div className='flex flex-col items-center gap-4 text-center text-sm sm:text-base w-full'>
					<div className='space-x-2'>
						<a
							href='/'
							className='hover:underline hover:underline-offset-8 hover:text-white text-[lawngreen]'
						>
							© Point conference
						</a>
						|
						<a
							href='/speakers'
							className='hover:underline hover:underline-offset-8 hover:text-white text-[lawngreen]'
						>
							Partners
						</a>
					</div>

					<div className='text-center leading-relaxed max-w-md sm:max-w-full'>
						Tawasoul Organization for Youth Empowerment · Iraq – Baghdad –
						Karradah city · Mobile: +964 770 790 6400
					</div>

					<a
						href='/'
						className='hover:underline hover:underline-offset-8 hover:text-white text-[lawngreen]'
					>
						point-iraq.org
					</a>
				</div>
			</div>
		</section>
	);
}
