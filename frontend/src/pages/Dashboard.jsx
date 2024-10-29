import '../App.css';
import PageLayout from "../components/PageLayout";
import {useContext, useEffect, useRef, useState} from "react";
import {UserContext} from "../Utils/UserContext";
import AddExpenseModal from "../components/addExpenseModal";
import ApexCharts from 'apexcharts'


function Dashboard() {

    const { userData, stats, loading, refreshUserData } = useContext(UserContext);

    const [open, setOpen] = useState(false)

    // Ref for the chart div
    const chartRef = useRef(null);

    const handleClose = () => {
        setOpen(false);
    };

    const handleOpen = () => {
        setOpen(true);
    };

    useEffect(() => {
        if (chartRef.current) {

            // const categories = [...new Set(userData.expenses.map(expense => expense.category.name || "No category"))];
            // const series = categories.map(category => {
            //     return userData.expenses
            //         .filter(expense => (expense.category.name || "No category") === category)
            //         .reduce((total, expense) => total + parseFloat(expense.amount), 0);
            // });

            const categories = ['Active', 'Unactive',];
            const series = [3, 2]


            const options = {
                chart: {
                    type: 'donut',
                    weight: 'auto',
                    height: 'auto',
                    foreColor: '#ffffff',
                    fontFamily: 'Roboto',
                },
                legend: {
                    position: 'bottom',
                    fontSize: '20px',
                },
                colors: ['#31a908', '#ff0000'],
                dataLabels: {
                    enabled: true, // Enable data labels if you want
                },
                labels: categories,
                series: series,
                xaxis: {
                    style:{
                        fontSize: `15px`
                    }
                },
            };

            const chart = new ApexCharts(chartRef.current, options);
            chart.render();

            // Cleanup function to destroy the chart on component unmount
            return () => {
                chart.destroy();
            };
        }
    }, [userData]); // Empty dependency array ensures this runs only once on mount

    if (loading) return <p>Loading...</p>;

    return (
        <>
            <PageLayout>

                <div className='flex flex-row justify-center items-center space-x-36 mt-16'>

                    <div className='flex flex-col space-y-10'>

                        <div className='flex flex-row space-x-[62px]'>
                            <div className='border rounded-[16px] border-[#000000] bg-[#000000] w-[713px] h-[248px] py-5 px-7 space-y-[52px]'>
                                <div className='flex flex-row'>
                                    <p className='text-[24px] font-thin text-white'>Expense Overview</p>

                                    <div className='flex-1'></div>

                                    <div className='space-x-2'>
                                        <button className='border border-[#0085FF] w-[127px] h-[36px] rounded-lg text-white'>
                                            Change plans
                                        </button>
                                        <button className='border border-[#0085FF] bg-[#0085FF] w-[168px] h-[36px] rounded-lg text-white'>
                                            Add subscription +
                                        </button>
                                    </div>
                                </div>

                                <div className='flex flex-row'>
                                    <div className='bg-[#0085FF] w-[168px] h-[106px] rounded-[10px]'>
                                        <p className='text-[19px] font-thin text-white text-center mt-[10px]'>Your balance</p>

                                        <p className='text-[35px] font-thin text-white text-center mt-[10px]'>$ 1205</p>
                                    </div>

                                    <div className='flex-1'></div>

                                    <div className='flex flex-row my-auto space-x-[56px]'>
                                        <div className='w-[152px] h-[82px]'>
                                            <p className='text-[18px] font-thin text-white text-center mt-[5px]'>Last 6 months</p>

                                            <p className='text-[30px] font-thin text-white text-center mt-[7.2px]'>$ 4800</p>
                                        </div>

                                        <div className='bg-gradient-to-b from-[#9000E9] to-[#6562EE] w-[138px] h-[82px] rounded-[10px]'>
                                            <p className='text-[18px] font-thin text-white text-center mt-[5px]'>Per month</p>

                                            <p className='text-[30px] font-thin text-white text-center mt-[7.2px]'>$ 8000</p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div className='border rounded-[16px] border-[#000000] bg-[#000000] w-[249px] h-[248px]'>
                                <p className='text-2xl font-bold text-white mt-3 text-center'>Total subscription</p>

                                <div className='' ref={chartRef} id="chart"></div>
                            </div>
                        </div>

                        {/*  last 6 months spending  */}
                        <div className='border rounded-[16px] border-[#000000] bg-[#000000] w-[1024px] h-[574px]'>

                        </div>
                    </div>

                    <div className='flex flex-col space-y-5'>

                        <div className='flex flex-col rounded-[16px] space-x-2 border border-[#000000] bg-[#000000] w-[314px] h-[418px]'>
                        </div>

                        <div className='flex flex-col'>

                            <p className='text-2xl font-bold text-white mb-6'>Upcoming reminders</p>

                            <div className='flex flex-col space-y-4'>
                                <div className='border rounded-[16px] border-[#000000] bg-[#000000] w-[315px] h-[182px]'>

                                </div>

                                <div className='border rounded-[16px] border-[#000000] bg-[#000000] w-[315px] h-[182px]'>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div className='flex flex-col justify-center items-center space-x-2'>
                    {/*<div className="flex flex-row justify-end items-end mt-16">
                        <button onClick={handleOpen} className='border bg-red-500 px-5 py-3 rounded-2xl text-white font-bold hover:bg-red-900 ease-in-out duration-300'>Add Expense</button>
                    </div>

                    <div className="flex flex-row justify-center items-center mt-16 space-x-28">

                        <div className="relative overflow-x-auto">
                            <p className='text-3xl font-medium mb-3'>Graph</p>
                            <div ref={chartRef} id="chart"></div>
                        </div>

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
                                {[...userData.expenses]
                                    .sort((a, b) => new Date(a.date) - new Date(b.date))
                                    .map((expense) => (
                                        <tr className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                            key={expense.title}>
                                            <th scope="row"
                                                className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {expense.title}
                                            </th>
                                            <td className="px-6 py-4">
                                                ${parseFloat(expense.amount).toFixed(2)}
                                            </td>
                                            <td className="px-6 py-4">
                                                {new Date(expense.date).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4">
                                                {expense.category.name || "No category"}
                                            </td>
                                            <td className="px-6 py-4">
                                                {expense.description || "No description"}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>*/}
                    {/*    /!*<div className='border border-gray-400 w-full h-16 px-44 justify-center items-center'>*!/*/}
                    {/*    /!*    There will be graph*!/*/}
                    {/*    /!*</div>*!/*/}
                    {/*</div>*/}

                </div>

                <AddExpenseModal isOpen={open} onClose={handleClose} refresh={refreshUserData}/>
            </PageLayout>
        </>
    );
}

export default Dashboard;
