import React from 'react';
import {Image, SafeAreaView, Text, View} from "react-native";

import {Redirect, router} from 'expo-router'

import images from '../constants/images'
import CustomButton from "../components/CustomButton";
import {useGlobalContext} from "../context/GlobalProvider";

const Index = () => {

    // const { loading, isLogged } = useGlobalContext();
    //
    // if (!loading && isLogged) return <Redirect href="/home" />;

    return (
        <SafeAreaView className="bg-Violet/Violet100 h-full">
            <View className='w-full justify-center items-center px-4 h-full'>
                <Text className='text-4xl text-white font-abold text-center'>Welcome to the Expense Tracker App</Text>
                <CustomButton
                    title="Continue with Email"
                    handlePress={() => router.push('/sign-in')}
                    containerStyles="w-full mt-24 bg-white"
                    textStyles="text-dark"
                />
            </View>
        </SafeAreaView>
    );
};

export default Index;