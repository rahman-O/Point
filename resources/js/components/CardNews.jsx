import {Card, CardBody, CardFooter} from "@nextui-org/react";

export const CardNews = ({desc_en, event_time, image}) => {
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
                <b>{event_time}</b>
                <p className="">{desc_en}</p>
            </CardFooter>
        </Card>
    );
}
