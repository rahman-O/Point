import { Link } from 'react-router-dom';
const stripHtmlTags = (content) => {
	const div = document.createElement('div');
	div.innerHTML = content;
	return div.textContent || div.innerText || '';
};
export const CardSpeaker = ({ id, name, job, image, desc }) => {
	return (
		<Link to={`/speakers/${id}`}>
			<div className='p-1'>
				<div className='overflow-visible p-0'>
					{/* Same 4:5 frame on every screen size. Photos uploaded through the dashboard are
					    already 4:5; older portraits are kept top-aligned so faces stay in view. */}
					<img
						width='100%'
						className='w-full aspect-[4/5] object-cover object-top'
						src={`/api/images/${image}`}
						alt={name}
						loading='lazy'
					/>
				</div>
				<div className='text-xl grid mt-4 '>
					<b>{name}</b>
					{job && <p className='text-sm text-gray-500'>{job}</p>}
				</div>
			</div>
		</Link>
	);
};
