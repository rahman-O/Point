
import { FaEarthAmericas } from "react-icons/fa6";
import { Card } from "flowbite-react";
export const CardSpeaker = ({name, job_en, image,conutry}) => {
    return (
        <Card
          className="max-w-sm mr-[3]"
          imgAlt="Meaningful alt text for an image that is not purely decorative"
          imgSrc={`/api/images/${image}`}
        >
          <h5 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
           {name}
          </h5>
          <p className="font-normal text-gray-700 dark:text-gray-400">
            {job_en}
          </p>
          <dev className="flex flex-row">
          <FaEarthAmericas size={10} className="m-2"/> 
          <p className="font-small text-gray-700 dark:text-gray-400">
             {conutry}
         </p>
          </dev>
         
        </Card>
      );
}
