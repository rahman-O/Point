export const CardSpeaker = ({name, job_en, image}) => {
    return (

        <div className=" rounded-none p-1 ">
            <div className="overflow-visible p-0">
                <img
                    width="100%"
                    className="w-full object-cover h-[180px] "
                    src={`/api/images/${image}`}
                />
            </div>
            <div className="text-small grid">
                <b>{name}</b>
                <p className="text-gray-500">{job_en}</p>

            </div>
        </div>
    );
}
