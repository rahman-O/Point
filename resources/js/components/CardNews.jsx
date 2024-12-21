import { Card, CardBody, CardFooter } from '@nextui-org/react';
import { Link } from 'react-router-dom';

export const CardNews = ({ id, title, desc_en, event_time, image }) => {
	return (
		<Link to={`/news/${id}`}>
			<Card shadow='sm' className=' rounded'>
				<CardBody className='overflow-visible p-0'>
					<img
						width='100%'
						className='w-full object-cover h-[180px] shadow-sm rounded-t'
						src={`/api/images/${image}`}
					/>
				</CardBody>
				<CardFooter className='text-small grid'>
					<b>{event_time}</b>
					<p className=''>{title} </p>
				</CardFooter>
			</Card>
		</Link>
	);
};
