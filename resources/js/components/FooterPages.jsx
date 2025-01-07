import React, { useContext, useEffect, useState } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function FooterPages() {
	const { lang, toggleLang } = useContext(LangContext);
	const [orgs, setOrgs] = useState([]);
	const [partners, setPartners] = useState([]);

	const fetcOrgshData = async () => {
		let res = await fetch('/api/orgs');
		let data = await res.json();
		setOrgs(data);
	};
	const fetchPartnersData = async () => {
		let res = await fetch('/api/partners');
		let data = await res.json();
		setPartners(data);
	};

	useEffect(() => {
		fetcOrgshData();
		fetchPartnersData();
	}, []);

	return (
		<section className='bg-black py-12'>
			<div className='text-white flex justify-center flex-col items-center gap-8'>
				<div class='flex flex-col justify-center items-center gap-4'>
					<h1 className='font-bold text-3xl tracking-widest'>Organizers</h1>
					<div className='flex flex-wrap justify-center'>
						{orgs.map((org) => (
							<div>
								<img
									className='sm:h-14 h-8 md:h-14'
									src={`/api/images/${org.image}`}
								/>
							</div>
						))}
					</div>
				</div>
				<div class='flex flex-col justify-center items-center gap-4'>
					<h1 className='font-bold text-3xl tracking-widest'>Partners</h1>
					<div className='flex flex-wrap justify-center'>
						{partners.map((org) => (
							<div>
								<img
									className='sm:h-24 h-16 md:h-22'
									src={`/api/images/${org.image}`}
								/>
							</div>
						))}
					</div>
				</div>
				<div class='flex flex-col justify-center items-center'>
					<div>
						{' '}
						<span class='hover:underline hover:underline-offset-8 hover:text-white text-[lawngreen]'>
							{' '}
							<a href='/'> © Point conference</a>
						</span>{' '}
						|{' '}
						<span class=' hover:underline hover:underline-offset-8  hover:text-white text-[lawngreen]'>
							{' '}
							<a href='/speakers'>Partners</a>
						</span>
					</div>
					<div className=' sm:text-xl text-medium md:text-lg flex justify-center items-center text-center gap-3 '>
						Tawasoul Organization for Youth Empowerment . Iraq – Baghdad –
						Karradah city . Mobile: +964 770 790 6400
					</div>
					<div class='hover:underline hover:underline-offset-8 hover:text-white text-[lawngreen]'>
						<a href='/'>point-iraq.org</a>
					</div>
				</div>
			</div>
		</section>
	);
}
