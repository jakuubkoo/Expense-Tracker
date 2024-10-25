import { TouchableOpacity, Text } from 'react-native'
import React from 'react'

export default function CustomButton({ title, handlePress, containerStyles, textStyles, isLoading }) {
    return (
        <TouchableOpacity
            onPress={handlePress}
            activeOpacity={0.7}
            className={`rounded-xl min-h-[62px] justify-center items-center ${containerStyles} ${isLoading ? 'opacity-50' : ''}`}
            disabled={isLoading}
        >
            <Text className={`font-asemibold text-lg ${textStyles}`}>{title}</Text>
        </TouchableOpacity>
    )
}