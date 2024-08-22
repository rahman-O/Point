
import {Card, CardHeader, CardBody} from "@nextui-org/card";
import {Image} from "@nextui-org/react";



export const CardSpeaker = ({name, conutary,image}) => {
    return(
        <Card className=" bg-transparent">
            <CardBody className="overflow-visible py-0 max-w-[200px]">
                <Image
                    alt="Card background"
                    className="object-cover rounded-xl"
                    src={`/api/images/${image}`}
                    style={{ aspectRatio: '1 / 1', width: '100%', padding: 0 }} // Ensure no padding
                />
            </CardBody>
            <CardHeader className="pb-0 pt-2 px-4 flex-col items-start">
                <p className="text-tiny uppercase font-bold">{name}</p>
                <small className="text-default-500">{conutary}</small>
            </CardHeader>
        </Card>

    )
}
