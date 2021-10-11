import React, {
    useEffect,
    useState
} from "react";
import api from "../../services/api";
import {useParams} from "react-router";

export default function with__MODEL__(WrappedComponent) {
    return function (props) {
        const [entity, setEntity] = useState(false);
        const {id} = useParams();

        useEffect(() => {
            const fetchData = async () => {
                const res = await api.__LC_MODEL__.get(id);
                setEntity(res.data);
            };
            fetchData();
        }, [id, setEntity]);

        return <WrappedComponent entity={entity} {...props} />;
    }
}
