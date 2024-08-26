import {Card, CardBody, CardFooter} from "@nextui-org/react";

export const CardSpeaker = ({name, job_en, image, conutry}) => {
    return (

        <Card shadow="sm">
            <CardBody className="overflow-visible p-0">
                <img
                    width="100%"
                    className="w-full object-cover h-[140px] shadow-sm rounded-lg"
                    src={`/api/images/${image}`}
                />
            </CardBody>
            <CardFooter className="text-small  grid">
                <b>{name}</b>
                <p className="text-default-500">{job_en}</p>
            </CardFooter>
        </Card>
    );
}
