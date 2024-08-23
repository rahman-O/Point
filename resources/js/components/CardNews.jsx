import {Image} from "@nextui-org/react";


export const CardNews = ({desc_en, event_time, image}) => {
    return (
        <div className=" overflow-hidden py-12 ">
            <div>
                <Image
                    alt="Card background"
                    className="object-cover rounded-md  w-5/6 h-5/6"
                    src={`/api/images/${image}`}
                />
            </div>
            {/*</div>*/}
            <div className="pb-0 pt-2 px-4 flex-col items-start">
                <p className=" text-lg uppercase text-gray-500">{event_time}</p>
                <p className="text-medium font-bold">{desc_en}</p>
            </div>
        </div>

    )
}
