import {Card, CardBody, CardFooter} from "@nextui-org/react";

export const CardSpeaker = ({name, job_en, image}) => {
    return (

        <Card shadow="sm">
            <CardBody className="overflow-visible p-0">
                <img
                    width="100%"
                    className="w-full object-cover h-[180px] shadow-sm rounded-t-lg"
                    src={`/api/images/${image}`}
                />
            </CardBody>
            <CardFooter className="text-small grid">
                <b>{name}</b>
                <p className="text-gray-500">{job_en}</p>
            </CardFooter>
        </Card>
    );
}
