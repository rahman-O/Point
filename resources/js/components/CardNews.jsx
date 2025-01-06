import { Card, CardBody, CardFooter } from '@nextui-org/react';
import { Link } from 'react-router-dom';

export const CardNews = ({ id, title, desc_en, event_time, image }) => {
	return (
		<Link to={`/news/${id}`}>
			<div className='  '>
				<div className='overflow-visible p-0'>
					<img
						width='100%'
						className='w-full object-cover h-[180px] '
						src={`/api/images/${image}`}
					/>
				</div>
				<div className=' grid mt-2'>
					<b className='text-gray-500 bold'>{event_time}</b>
					<p className='text-lg'>{title} </p>
				</div>
			</div>
		</Link>
	);
};
