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
		<div className='MainFooter  bg-black'>
			<div className='h-75 bg-black text-white flex justify-center flex-col items-center mt-12'>
				<h1 className='font-bold text-xl py-4 '>Partners</h1>
				<div className='flex flex-wrap justify-center'>
					{partners.map((org) => (
						<div>
							<img
								className='sm:h-14 h-8 md:h-14'
								src={`/api/images/${org.image}`}
							/>
						</div>
					))}
				</div>{' '}
				<h1 className='font-bold text-xl'>Organizers</h1>
				<div className='flex flex-wrap justify-center'>
					{orgs.map((org) => (
						<div>
							<img
								className='sm:h-14 h-8 md:h-12'
								src={`/api/images/${org.image}`}
							/>
						</div>
					))}
				</div>
				<div className='py-2 sm:text-xl text-medium md:text-lg flex justify-center items-center '>
					Tawasoul Organization for Youth Empowerment . Iraq – Baghdad –
					Karradah city . Mobile: +964 770 790 6400
				</div>
			</div>
		</div>
	);
}
