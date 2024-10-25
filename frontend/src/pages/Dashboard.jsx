import '../App.css';
import PageLayout from "../components/PageLayout";
import {useContext, useState} from "react";
import {UserContext} from "../Utils/UserContext";
import AddExpenseModal from "../components/addExpenseModal";

function Dashboard() {

    const { userData, stats, loading, refreshUserData } = useContext(UserContext);

    const [open, setOpen] = useState(false)

    const handleClose = () => {
        setOpen(false);
    };

    const handleOpen = () => {
        setOpen(true);
    };

    if (loading) return <p>Loading...</p>;

    return (
        <>
            <PageLayout>

                <div className='flex flex-col justify-center items-center space-x-2'>

                    <div className="flex flex-row justify-end items-end mt-16">
                        <button onClick={handleOpen} className='border bg-red-500 px-5 py-3 rounded-2xl text-white font-bold hover:bg-red-900 ease-in-out duration-300'>Add Expense</button>
                    </div>

                    <div className="flex flex-row justify-center items-center mt-16 space-x-28">


                        <div className="relative overflow-x-auto">
                            <p className='text-3xl font-medium mb-3'>All Expenses</p>
                            <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" className="px-6 py-3">
                                        Title
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Amount
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Date
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Category
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Description
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                {userData.expenses.map((expense) => (
                                    <tr className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                        key={expense.title}>
                                        <th scope="row"
                                            className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {expense.title}
                                        </th>
                                        <td className="px-6 py-4">
                                            {expense.category.name || "No category"}
                                        </td>
                                        <td className="px-6 py-4">
                                            {expense.description || "No description"}
                                        </td>
                                        <td className="px-6 py-4">
                                            ${parseFloat(expense.amount).toFixed(2)}
                                        </td>
                                        <td className="px-6 py-4">
                                            {new Date(expense.date).toLocaleDateString()}
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>


                        {/*<div className='border border-gray-400 w-full h-16 px-44 justify-center items-center'>*/}
                        {/*    There will be graph*/}
                        {/*</div>*/}

                    </div>

                </div>

                <AddExpenseModal isOpen={open} onClose={handleClose} refresh={refreshUserData}/>
            </PageLayout>
        </>
    );
}

export default Dashboard;
